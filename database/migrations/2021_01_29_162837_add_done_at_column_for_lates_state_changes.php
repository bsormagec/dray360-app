<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDoneAtColumnForLatesStateChanges extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_job_latest_state', function (Blueprint $table) {
            $table->dateTime('done_at')->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('t_job_latest_state', function (Blueprint $table) {
            $table->dropColumnIfExists('done_at');
        });
    }
}
