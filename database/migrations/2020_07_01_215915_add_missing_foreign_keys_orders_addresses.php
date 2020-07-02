<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMissingForeignKeysOrdersAddresses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_orders', function (Blueprint $table) {
            $table->foreign('bill_to_address_id')->references('id')->on('t_addresses');
            $table->foreign('port_ramp_of_origin_address_id')->references('id')->on('t_addresses');
            $table->foreign('port_ramp_of_destination_address_id')->references('id')->on('t_addresses');
            $table->foreign('t_tms_provider_id')->references('id')->on('t_tms_providers');
            $table->foreign('t_company_id')->references('id')->on('t_companies');
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
            $table->dropForeign(['bill_to_address_id']);
            $table->dropForeign(['port_ramp_of_origin_address_id']);
            $table->dropForeign(['port_ramp_of_destination_address_id']);
            $table->dropForeign(['t_tms_provider_id']);
            $table->dropForeign(['t_company_id']);
        });
    }
}
