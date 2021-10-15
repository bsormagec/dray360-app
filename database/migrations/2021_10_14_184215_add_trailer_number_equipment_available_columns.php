<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTrailerNumberEquipmentAvailableColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_orders', function (Blueprint $table) {
            $table->string('trailer_number', 64)->nullable();

            $table->dateTime('equipment_available_date')->nullable();
            $table->time('equipment_available_time')->nullable();
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
            $table->dropColumnIfExists('trailer_number');
            $table->dropColumnIfExists('equipment_available_date');
            $table->dropColumnIfExists('equipment_available_time');
        });
    }
}
