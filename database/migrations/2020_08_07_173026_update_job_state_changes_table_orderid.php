<?php

use Illuminate\Database\Migrations\Migration;

class UpdateJobStateChangesTableOrderid extends Migration
{
    const TEMPUPDATESTATESTRIGGER = <<<ENDOFSQL
        drop trigger if exists t_job_state_change_updated;
        create trigger t_job_state_change_updated
        after update on t_job_state_changes
        for each row
            if exists (select id from t_job_latest_state where t_job_latest_state.request_id = new.request_id and coalesce(t_job_latest_state.order_id, -1) = coalesce(new.order_id, -1)) then
                update t_job_latest_state
                set t_job_latest_state.t_job_state_changes_id = new.id
                where t_job_latest_state.request_id = new.request_id
                and coalesce(t_job_latest_state.order_id, -1) = coalesce(new.order_id, -1);
            else
                insert into t_job_latest_state
                (t_job_state_changes_id, request_id, order_id) values
                (new.id, new.request_id, new.order_id);
            end if;
    ENDOFSQL;

    const ADDORDERIDSQL = <<<ENDOFSQL
        -- For status 'ocr-post-processing-complete' simply look up id from order
        -- and use it's request_id. Match on t_order.updated_at date to t_job_state_changes.created_at
        -- as a way of dealing with multiple orders created by a single request (crude but sufficient
        -- at this stage of the project since the only multiple-orders we have are from testing).
        update t_orders as o
        join t_job_state_changes as j on o.request_id = j.request_id and o.updated_at = j.created_at
        set j.order_id = o.id
        where j.status = 'ocr-post-processing-complete'
        ;

        -- Using the just-made update, now make a table of all known requestid/orderid combos,
        -- taking MAX order_id when there are multiple order_ids for a single request_id.
        drop table if exists tmp_requestids_orderids;
        create temporary table tmp_requestids_orderids as
        select request_id, max(order_id) as order_id
        from t_job_latest_state
        where order_id is not null
        group by request_id
        ;

        -- Using the temporary table just created, now for the three WINT statuses,
        -- set their order_id to the latest order_id for that request_id.
        update t_job_state_changes as j
        join tmp_requestids_orderids as t on j.request_id = t.request_id
        set j.order_id = t.order_id
        where j.status in ('failure-sending-to-wint', 'sending-to-wint', 'success-sending-to-wint')
        ;

        -- the previous update created duplicate rows in t_job_latest_state with one row
        -- having an order_id and another having null, but both pointing to the same
        -- row in the t_job_status_changes table. Build a list of the ids of the rows having null order_id.
        drop table if exists tmp_duplicate_latest_state_rows;
        create temporary table tmp_duplicate_latest_state_rows as
        select id
        from t_job_latest_state
        where t_job_state_changes_id in (
            select t_job_state_changes_id
            from t_job_latest_state
            group by t_job_state_changes_id
            having count(*)>1
        )
        and order_id is null
        ;

        -- and delete the bad "null order_id" rows from t_job_latest_state
        delete l
        from t_job_latest_state as l
        join tmp_duplicate_latest_state_rows as t on l.id = t.id
        ;

    ENDOFSQL;

    /*
        -- Here are some test queries to view the results of the migration

        select id,request_id,order_id,company_id from t_job_state_changes where order_id is not null limit 10;
        select * from t_job_latest_state where request_id='ff232a21-e6e4-5e78-9bd3-7d1fb62b999a';
        select * from t_job_latest_state where request_id = 'f8978cf2-e986-539f-840b-db8afe34b719';

        select  l.request_id, j.status, l.order_id as l_order_id, j.order_id as j_order_id, l.id as t_job_latest_state_id, j.id as t_job_state_changes_id
        from t_job_latest_state as l
        join t_job_state_changes as j
        on l.t_job_state_changes_id = j.id
        where l.request_id in ('6b7cb6dc-14cc-5973-91f2-e1a6ef3db64b', 'f8978cf2-e986-539f-840b-db8afe34b719')
        order by l.request_id, j.id
        ;

        select * from t_job_state_changes where request_id = 'f8978cf2-e986-539f-840b-db8afe34b719' order by id \G
        select order_id, count(*) as c from t_job_latest_state group by order_id;
        select order_id, count(*) as c from t_job_state_changes group by order_id;
        select request_id, count(*) as c from t_job_latest_state group by request_id having c>1 order by c asc;
    */

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(UpdateJobStateChangesTableOrderid::TEMPUPDATESTATESTRIGGER);
        DB::unprepared(UpdateJobStateChangesTableOrderid::ADDORDERIDSQL);
        DB::unprepared('drop trigger if exists t_job_state_change_updated;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // nothing to undo. unroll the'2020_08_04_153706_add_company_and_order_to_statuskey'
        // migration and the updates done in this migration will be deleted.
    }
}
