<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexesToStateChangesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_job_state_changes', function (Blueprint $table) {
            $table->index(['request_id', 'order_id']);
            $table->index(['order_id', 'request_id']);
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
            $table->dropIndex(['request_id', 'order_id']);
            $table->dropIndex(['order_id', 'request_id']);
        });
    }
}
