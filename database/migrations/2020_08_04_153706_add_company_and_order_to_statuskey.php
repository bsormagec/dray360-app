<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Update the t_job_state_changes and t_job_latest_state tables to
 * add order_id and company_id columns plus indices.
 */
class AddCompanyAndOrderToStatuskey extends Migration
{

    // New latest-state trigger fires on every insert into t_job_state_changes
    const NEWLATESTSTATETRIGGER = <<<ENDOFSQL
        drop trigger if exists t_job_state_change_inserted;
        create trigger t_job_state_change_inserted
        after insert on t_job_state_changes
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

    // Old latest-state trigger (for rollback)
    const OLDLATESTSTATETRIGGER = <<<ENDOFSQL
        drop trigger if exists t_job_state_change_inserted;
        create trigger t_job_state_change_inserted
        after insert on t_job_state_changes
        for each row
            if exists (select * from t_job_latest_state where t_job_latest_state.request_id = new.request_id) then
                update t_job_latest_state
                set t_job_latest_state.t_job_state_changes_id = new.id
                where t_job_latest_state.request_id = new.request_id;
            else
                insert into t_job_latest_state
                (request_id, t_job_state_changes_id) values
                (new.request_id, new.id);
            end if;
    ENDOFSQL;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        // New columns (and index) for t_job_state_changes
        Schema::table(
            't_job_state_changes',
            function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable(false);
                $table->unsignedBigInteger('order_id')->nullable();
                $table->index(['request_id', 'company_id'], 'RequestCompanyIndex');
            }
        );

        // New columns for t_job_latest_state
        Schema::table(
            't_job_latest_state',
            function (Blueprint $table) {
                // make new order_id column
                $table->unsignedBigInteger('order_id')->nullable();

                // remove unique constraint on request_id, and make it a normal index
                $table->dropUnique('t_job_latest_state_request_id_unique');
                $table->index('request_id'); // make non-unique index for request_id

                // Make request_id/order_id combo key, required unique!
                $table->unique(['request_id', 'order_id'], 'RequestOrderIndex');
            }
        );

        // Updated trigger to save company_id and order_id (if present)
        DB::unprepared(AddCompanyAndOrderToStatuskey::NEWLATESTSTATETRIGGER);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        // Drop columns on t_job_state_changes
        if (Schema::hasColumn('t_job_state_changes', 'company_id')) {
            Schema::table(
                't_job_state_changes',
                function (Blueprint $table) {
                    $table->dropColumn('company_id');
                }
            );
        }
        if (Schema::hasColumn('t_job_state_changes', 'order_id')) {
            Schema::table(
                't_job_state_changes',
                function (Blueprint $table) {
                    $table->dropColumn('order_id');
                }
            );
        }

        // Drop combo index on t_job_state_changes
        Schema::table(
            't_job_latest_state',
            function (Blueprint $table) {
                $table->dropIndex('RequestOrderIndex');
            }
        );

        // Drop combo index on t_job_latest_state, and replace single index for request_id
        Schema::table(
            't_job_state_changes',
            function (Blueprint $table) {
                $table->dropIndex('RequestCompanyIndex');
            }
        );

        // Drop columns for t_job_latest_state
        if (Schema::hasColumn('t_job_latest_state', 'order_id')) {
            Schema::table(
                't_job_latest_state',
                function (Blueprint $table) {
                    $table->dropColumn('order_id');
                }
            );
        }

        // Replace unique requestid on t_job_latest_state
        Schema::table(
            't_job_latest_state',
            function (Blueprint $table) {
                $table->dropIndex('t_job_latest_state_request_id_index'); // remove non-unique index
                $table->unique('request_id');    // recreated unique index
            }
        );

        // Revert t_job_latest_state migration back to original
        // (i.e. see 2020_04_10_183457_create_ocrrequest_tables.php)
        DB::unprepared(AddCompanyAndOrderToStatuskey::OLDLATESTSTATETRIGGER);
    }
}
