<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateOrderAddressColumns extends Migration
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
            $table->boolean('bill_to_address_verified')->nullable();
            $table->text('bill_to_address_raw_text')->nullable();

            $table->boolean('port_ramp_of_origin_address_verified')->nullable();
            $table->text('port_ramp_of_origin_address_raw_text')->nullable();

            $table->boolean('port_ramp_of_destination_address_verified')->nullable();
            $table->text('port_ramp_of_destination_address_raw_text')->nullable();
        });

        // t_order_address_events
        Schema::table('t_order_address_events', function (Blueprint $table) {
            $table->unsignedBigInteger('t_address_id')->nullable()->change();
            $table->boolean('t_address_verified')->nullable();
            $table->text('t_address_raw_text')->nullable();
        });

        // drop column from t_addresses table
        Schema::table('t_addresses', function (Blueprint $table) {
            $table->dropColumn('unparsed_text_block');
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
            $table->dropColumn('bill_to_address_verified');
            $table->dropColumn('bill_to_address_raw_text');
            $table->dropColumn('port_ramp_of_origin_address_verified');
            $table->dropColumn('port_ramp_of_origin_address_raw_text');
            $table->dropColumn('port_ramp_of_destination_address_verified');
            $table->dropColumn('port_ramp_of_destination_address_raw_text');
        });

        // drop columns from t_order_address_events table
        Schema::table('t_order_address_events', function (Blueprint $table) {
            $table->dropColumn('t_address_verified');
            $table->dropColumn('t_address_raw_text');
        });

        // replace column from t_addresses table
        Schema::table('t_addresses', function (Blueprint $table) {
            $table->text('unparsed_text_block')->nullable();
        });
    }
}
