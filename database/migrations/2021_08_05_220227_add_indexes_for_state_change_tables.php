<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexesForStateChangeTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_job_state_changes', function (Blueprint $table) {
            $table->index('order_id');
            $table->index('company_id');
        });
        Schema::table('t_job_latest_state', function (Blueprint $table) {
            $table->index('order_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('t_job_state_changes', function (Blueprint $table) {
            $table->dropIndex(['order_id']);
            $table->dropIndex(['company_id']);
        });
        Schema::table('t_job_latest_state', function (Blueprint $table) {
            $table->dropIndex(['order_id']);
        });
    }
}
