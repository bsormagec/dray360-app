<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPtequipmentToTOrders extends Migration
{

    const SET_PTEQUIP_CONTAINER_DICTIDS = <<<ENDOFSQL
        update t_orders as o
        left join t_equipment_types as e on (o.t_equipment_type_id = e.id)
        left join t_dictionary_items as d on (e.tms_equipment_id = d.item_key and o.t_company_id = d.t_company_id and d.item_type='pt-equipmenttype' and d.deleted_at is null)
        set o.pt_equipmenttype_container_dictid = d.id
        where o.t_equipment_type_id is not null
          and o.t_tms_provider_id = 1
          and o.pt_equipmenttype_container_dictid is null
          and d.id is not null
        ;
    ENDOFSQL;

    const SET_PTEQUIP_CHASSIS_DICTIDS = <<<ENDOFSQL
        update t_orders as o
        left join t_equipment_types as e on (o.chassis_equipment_type_id = e.id)
        left join t_dictionary_items as d on (e.tms_equipment_id = d.item_key and o.t_company_id = d.t_company_id and d.item_type='pt-equipmenttype' and d.deleted_at is null)
        set o.pt_equipmenttype_chassis_dictid = d.id
        where o.chassis_equipment_type_id is not null
          and o.t_tms_provider_id = 1
          and o.pt_equipmenttype_chassis_dictid is null
          and d.id is not null
        ;
    ENDOFSQL;

    const SHOW_RESULTS_OF_PTEQUP_UPDATES = <<<ENDOFSQL
        select
             o.id as order_id
            ,o.t_company_id
            ,' ' as filler
            
            ,o.t_equipment_type_id
            ,econ.tms_equipment_id as container_tms_id
            ,econ.equipment_type_and_size as container_type_and_size
            ,' ' as filler
            
            ,o.chassis_equipment_type_id
            ,echa.tms_equipment_id as chassis_tms_id
            ,echa.equipment_type_and_size as chassis_type_and_size
            ,' ' as filler
            
            ,dcon.id as tbd_container_dictid
            ,dcon.item_key as tbd_container_dict_item_key
            ,dcon.item_display_name as tbd_container_dict_display_name
            ,' ' as filler
            
            ,dcha.id as tbd_chassis_dictid
            ,dcha.item_key as tbd_chassis_dict_item_key
            ,dcha.item_display_name as tbd_container_dict_display_name
            ,' ' as filler
            
            ,o.pt_equipmenttype_container_dictid
            ,o.pt_equipmenttype_chassis_dictid
            
        from t_orders as o
        left join t_equipment_types as econ on (o.t_equipment_type_id       = econ.id and econ.deleted_at is null)
        left join t_equipment_types as echa on (o.chassis_equipment_type_id = echa.id and echa.deleted_at is null)
        left join t_dictionary_items as dcon on (econ.tms_equipment_id = dcon.item_key and o.t_company_id = dcon.t_company_id and dcon.item_type='pt-equipmenttype' and dcon.deleted_at is null)
        left join t_dictionary_items as dcha on (echa.tms_equipment_id = dcha.item_key and o.t_company_id = dcha.t_company_id and dcha.item_type='pt-equipmenttype' and dcha.deleted_at is null)
        
        where true
          and coalesce(o.t_equipment_type_id, o.chassis_equipment_type_id) is not null
          and coalesce(o.pt_equipmenttype_container_dictid, o.pt_equipmenttype_chassis_dictid) is not null
        order by o.id desc 
        ;
    ENDOFSQL;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_orders', function (Blueprint $table) {
            $table->unsignedBigInteger('pt_equipmenttype_container_dictid')->nullable();
            $table->boolean('pt_equipmenttype_container_dictid_verified')->nullable();
            $table->foreign('pt_equipmenttype_container_dictid')->references('id')->on('t_dictionary_items');

            $table->unsignedBigInteger('pt_equipmenttype_chassis_dictid')->nullable();
            $table->boolean('pt_equipmenttype_chassis_dictid_verified')->nullable();
            $table->foreign('pt_equipmenttype_chassis_dictid')->references('id')->on('t_dictionary_items');
        });

        Schema::table('t_orders', function (Blueprint $table) {
            DB::unprepared(AddPtequipmentToTOrders::SET_PTEQUIP_CONTAINER_DICTIDS);
            DB::unprepared(AddPtequipmentToTOrders::SET_PTEQUIP_CHASSIS_DICTIDS);
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
            $table->dropColumnIfExists('pt_equipmenttype_container_dictid_verified');
            $table->dropColumnIfExists('pt_equipmenttype_container_dictid');

            $table->dropColumnIfExists('pt_equipmenttype_chassis_dictid_verified');
            $table->dropColumnIfExists('pt_equipmenttype_chassis_dictid');
        });
    }
}
