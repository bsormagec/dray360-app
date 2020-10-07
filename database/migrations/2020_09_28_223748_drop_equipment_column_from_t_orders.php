<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropEquipmentColumnFromTOrders extends Migration
{
    const NEWEQUIPMENTDISPLAYFORMULA = <<<ENDOFSQL
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
    ENDOFSQL;

    const OLDEQUIPMENTDISPLAYFORMULA = <<<ENDOFSQL
        if (row_type = 'combined',
            equipment_type_and_size,
            if (row_type = 'separate',
                concat(equipment_type, ' ',equipment_size),
                ''
            )
        )
    ENDOFSQL;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // drop then update new for t_equipment_types.equipment_display column
        // which will prefix the equipment_owner
        if (Schema::hasColumn('t_equipment_types', 'equipment_display')) {
            Schema::table('t_equipment_types', function (Blueprint $table) {
                $table->dropColumn('equipment_display');
            });
        }
        Schema::table('t_equipment_types', function (Blueprint $table) {
            $table->string('equipment_display')->storedAs(DropEquipmentColumnFromTOrders::NEWEQUIPMENTDISPLAYFORMULA);
        });

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

        // add column t_equipment_types.scac
        Schema::table('t_equipment_types', function (Blueprint $table) {
            $table->string('scac', 64)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // drop then restore original t_equipment_types.equipment_display column
        if (Schema::hasColumn('t_equipment_types', 'equipment_display')) {
            Schema::table('t_equipment_types', function (Blueprint $table) {
                $table->dropColumn('equipment_display');
            });
        }
        Schema::table('t_equipment_types', function (Blueprint $table) {
            $table->string('equipment_display')->storedAs(DropEquipmentColumnFromTOrders::NEWEQUIPMENTDISPLAYFORMULA);
        });

        // restore t_orders.equipment
        Schema::table('t_orders', function (Blueprint $table) {
            $table->string('equipment', 64)->nullable();
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

        // drop t_equipment_types.scac
        if (Schema::hasColumn('t_equipment_types', 'scac')) {
            Schema::table('t_equipment_types', function (Blueprint $table) {
                $table->dropColumn('scac');
            });
        }
    }
}
