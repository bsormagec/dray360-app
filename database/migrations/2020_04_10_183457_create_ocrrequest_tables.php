<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOcrrequestTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_job_state_changes', function (Blueprint $table) {
            $table->bigIncrements('id', true, true);
            $table->string('request_id', 512);
            $table->datetime('status_date');
            $table->string('status', 32)->nullable();
            $table->json('status_metadata')->nullable();

            // not using $table->timestamps(); because these columns need updating
            // from outside the laravel server, i.e. from a AWS lambda script
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->default(DB::raw('NULL ON UPDATE CURRENT_TIMESTAMP'))->nullable();

            $table->index(['request_id']);
            $table->index(['status']);
            $table->index(['status_date']);
            $table->index(['created_at']);
            $table->index(['updated_at']);
        });

        // only one entry per request_id can be the "latest" for that request_id
        Schema::create('t_job_latest_state', function (Blueprint $table) {
            $table->bigIncrements('id', true, true);
            $table->string('request_id', 512)->unique(); // this creates an index
            $table->bigInteger('t_job_state_changes_id', false, true);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->default(DB::raw('NULL ON UPDATE CURRENT_TIMESTAMP'))->nullable();

            $table->index(['created_at']);
            $table->index(['updated_at']);

            // add foreign key constraints
            $table->foreign('t_job_state_changes_id')->references('id')->on('t_job_state_changes');
        });

        // create the insert trigger on t_job_state_changes to upsert the t_job_latest_state row.
        // in other words, the t_job_latest_state row identifies which t_job_state_changes row
        // was the latest update for a given request_id
        DB::unprepared("
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
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('drop trigger if exists t_job_state_change_inserted');
        Schema::dropIfExists('t_job_latest_state');
        Schema::dropIfExists('t_job_state_changes');
    }
}
