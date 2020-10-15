<?php

use Illuminate\Database\Migrations\Migration;

/**
 * Update the t_job_state_changes table to add order_id and company_id values.
 */
class UpdateJobStateChangesTableCompanyid extends Migration
{

    //
    // sql to add company_id to all existing t_job_state_changes rows
    // special values:
    //     0 = company not knowable at this point in the workflow
    //     999999999 = company not identified (bad old data)
    //

    const UPDATECOMPANYSQL = <<<ENDOFSQL
        create temporary table t_tmp_request_company_migration_list as
        select distinct
        request_id
        ,if(company_id_count > 1, 2, company_id_list) as company_id -- company_id '2' is tcompanies-demo
        from (
        select distinct
            request_id
            ,group_concat(distinct company_id) as company_id_list
            ,count(distinct company_id) as company_id_count
        from (

            -- main query that gets request_id/company_id from whereever it can be found
            select distinct request_id, company_id from (
                    select request_id, t_company_id as company_id from t_orders
                union select request_id, json_extract(status_metadata, '$.company_id') as company_id from t_job_state_changes where status = 'upload-requested'
                union select request_id, json_extract(status_metadata, '$.event_info.company_id') as company_id from t_job_state_changes where json_extract(status_metadata, '$.event_info.company_id') is not null and json_extract(status_metadata, '$.event_info.company_id') <> CAST('null' AS JSON)
                union select request_id, (select id from t_companies where json_extract(status_metadata, '$.event_info.recipient0') in (email_intake_address, email_intake_address_alt)) as company_id from t_job_state_changes where status = 'intake-started'
                union select request_id, 1 as company_id from t_job_state_changes where status_metadata like '%"recipient0": "cushing-test@docprocessing.draymaster.com"%'
                union select request_id, 1 as company_id from t_job_state_changes where status_metadata like '%"recipient0": "cushing-test@intake.dray360.com"%'

            -- end of main

            ) as subq1
            where company_id is not null
        ) as subq2
        group by request_id
        ) as subq3
        order by request_id
        ; -- runs in 7 seconds

        -- add index to above tmp table
        create index t_tmp_request_company_migration_list_request_id_index on t_tmp_request_company_migration_list(request_id);

        -- update t_job_state_changes
        update t_job_state_changes as j
        set j.company_id = coalesce(
        (select company_id from t_tmp_request_company_migration_list as t where j.request_id = t.request_id limit 1),
        999999999
        ); -- runs in 2.5 minutes

        update t_job_state_changes set company_id = 0 where status = 'intake-started';
    ENDOFSQL;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // run the big sql update - this will take a few minutes
        echo "Updating t_job_state_changes.company_id. This may take several minutes.";
        DB::unprepared(UpdateJobStateChangesTableCompanyId::UPDATECOMPANYSQL);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // nothing to do, the prior migration will delete the columns, if that's what's wanted
    }
}
