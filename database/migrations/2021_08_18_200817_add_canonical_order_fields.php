<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCanonicalOrderFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_orders', function (Blueprint $table) {
            $table->date('eta_date')->nullable();
            $table->time('eta_time')->nullable();
            $table->integer('temperature')->nullable();
            $table->string('required_equipment', 512)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('t_orders', function (Blueprint $table) {
            $table->dropColumnIfExists('eta_date');
            $table->dropColumnIfExists('eta_time');
            $table->dropColumnIfExists('temperature');
            $table->dropColumnIfExists('required_equipment');
        });
    }
}
