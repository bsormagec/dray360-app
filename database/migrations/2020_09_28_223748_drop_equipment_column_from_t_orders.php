<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropEquipmentColumnFromTOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // drop t_orders.equipment
        if (Schema::hasColumn('t_orders', 'equipment')) {
            Schema::table('t_orders', function (Blueprint $table) {
                $table->dropColumn('equipment');
            });
        }

        // drop t_orders.owner_or_ss_company
        if (Schema::hasColumn('t_orders', 'owner_or_ss_company')) {
            Schema::table('t_orders', function (Blueprint $table) {
                $table->dropColumn('owner_or_ss_company');
            });
        }

        // add t_orders.equipment_type_verified
        Schema::table('t_orders', function (Blueprint $table) {
            $table->boolean('equipment_type_verified')->nullable();
        });

        // update new formula for t_equipment_types.equipment_display column
        // to prefix the equipment_owner
        $storedColumnQuery = <<<'mysql'
            concat(
                equipment_owner,
                if (equipment_owner = '', '', ' '),
                if (row_type = 'combined',
                    equipment_type_and_size,
                    if (row_type = 'separate',
                        concat(equipment_type, ' ',equipment_size),
                        ''
                    )
                )
            )
        mysql;
        Schema::create('t_equipment_types', function (Blueprint $table) {
            $table->string('equipment_display')->storedAs($storedColumnQuery);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // restore t_orders.equipment_type
        Schema::table('t_orders', function (Blueprint $table) {
            $table->string('equipment_type', 64)->nullable();
        });

        // restore t_orders.owner_or_ss_company
        Schema::table('t_orders', function (Blueprint $table) {
            $table->string('owner_or_ss_company', 64)->nullable();
        });

        // drop t_orders.equipment_type_verified
        if (Schema::hasColumn('t_orders', 'equipment_type_verified')) {
            Schema::table('t_orders', function (Blueprint $table) {
                $table->dropColumn('equipment_type_verified');
            });
        }

        // restore original t_equipment_types.equipment_display column
        $storedColumnQuery = <<<'mysql'
            if (row_type = 'combined',
                equipment_type_and_size,
                if (row_type = 'separate',
                    concat(equipment_type, ' ',equipment_size),
                    ''
                )
            )
        mysql;
        Schema::create('t_equipment_types', function (Blueprint $table) {
            $table->string('equipment_display')->storedAs($storedColumnQuery);
        });
    }
}
