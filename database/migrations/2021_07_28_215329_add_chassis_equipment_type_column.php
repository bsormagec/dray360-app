<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddChassisEquipmentTypeColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_orders', function (Blueprint $table) {
            $table->unsignedBigInteger('chassis_equipment_type_id')->nullable();
            $table->boolean('chassis_equipment_type_verified')->nullable();

            $table->foreign('chassis_equipment_type_id')->references('id')->on('t_equipment_types');
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
            $table->dropColumnIfExists('chassis_equipment_type_id');
            $table->dropColumnIfExists('chassis_equipment_type_verified');
        });
    }
}
