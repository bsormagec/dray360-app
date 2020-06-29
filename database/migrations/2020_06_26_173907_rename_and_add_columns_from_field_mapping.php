<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameAndAddColumnsFromFieldMapping extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_orders', function (Blueprint $table) {
            $table->renameColumn('seal_number_list', 'seal_number');
            $table->renameColumn('bol', 'bill_of_lading');

            $table->string('carrier', 64)->nullable();
            $table->string('bill_charge', 64)->nullable();
            $table->string('bill_comment', 64)->nullable();
            $table->string('line_haul', 64)->nullable();
            $table->string('rate_box', 64)->nullable();
            $table->string('fuel_surcharge', 64)->nullable();
            $table->string('total_accessorial_charges', 64)->nullable();
            $table->string('equipment_provider', 64)->nullable();
            $table->string('actual_destination', 64)->nullable();
            $table->string('actual_origin', 64)->nullable();
            $table->string('customer_number', 64)->nullable();
            $table->string('expedite', 64)->nullable();
            $table->string('load_number', 64)->nullable();
            $table->string('purchase_order_number', 64)->nullable();
            $table->string('release_number', 64)->nullable();
            $table->string('ship_comment', 64)->nullable();
        });

        Schema::table('t_order_line_items', function (Blueprint $table) {
            $table->renameColumn('description', 'contents');
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
            $table->renameColumn('seal_number', 'seal_number_list');
            $table->renameColumn('bill_of_lading', 'bol');

            $table->dropColumn([
                'carrier',
                'bill_charge',
                'bill_comment',
                'line_haul',
                'rate_box',
                'fuel_surcharge',
                'total_accessorial_charges',
                'equipment_provider',
                'actual_destination',
                'actual_origin',
                'customer_number',
                'expedite',
                'load_number',
                'purchase_order_number',
                'release_number',
                'ship_comment',
            ]);
        });

        Schema::table('t_order_line_items', function (Blueprint $table) {
            $table->renameColumn('contents', 'description');
        });
    }
}
