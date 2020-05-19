<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
note: signature for $table->bigInteger('bill_to_address_id', false, true)
has as the two booleans, the meaning:
public function bigInteger($column, $autoIncrement = false, $unsigned = false)
but is better to use: $table->unsignedBigInteger('bill_to_address_id', false)
*/


class UpdateAddressAndOrdersTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // add columns to t_orders table
        Schema::table('t_orders', function (Blueprint $table) {
            $table->string('booking_number', 512)->nullable()->index();
            $table->string('bol', 64)->nullable();
            $table->unsignedBigInteger('bill_to_address_id')->nullable();
            $table->unsignedBigInteger('port_ramp_of_origin_address_id')->nullable();
            $table->unsignedBigInteger('port_ramp_of_destination_address_id')->nullable();
            $table->json('ocr_data')->nullable();

            $table->dropColumn('port_ramp_of_origin');
            $table->dropColumn('port_ramp_of_destination');
        });

        // add columns to t_addresses table
        Schema::table('t_addresses', function (Blueprint $table) {
            $table->text('unparsed_text_block', 4096)->nullable();
            $table->string('location_name', 512)->nullable();
        });

        // add columns to t_order_address_events table
        Schema::table('t_order_address_events', function (Blueprint $table) {
            $table->text('unparsed_event_type', 512)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // drop columns from t_orders table
        Schema::table('t_orders', function (Blueprint $table) {
            $table->dropColumn('booking_number');
            $table->dropColumn('bol');
            $table->dropColumn('bill_to_address_id');
            $table->dropColumn('port_ramp_of_origin_address_id');
            $table->dropColumn('port_ramp_of_destination_address_id');
            $table->dropColumn('ocr_data');

            $table->string('port_ramp_of_origin', 64)->nullable();
            $table->string('port_ramp_of_destination', 64)->nullable();
        });

        // drop columns from t_addresses table
        Schema::table('t_addresses', function (Blueprint $table) {
            $table->dropColumn('unparsed_text_block');
            $table->dropColumn('location_name');
        });

        // add columns to t_order_address_events table
        Schema::table('t_order_address_events', function (Blueprint $table) {
            $table->dropColumn('unparsed_event_type');
        });

    }
}
