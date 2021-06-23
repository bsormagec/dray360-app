<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCompcareColumnsToAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_addresses', function (Blueprint $table) {
            $table->boolean('is_cc_payor')->default(false);
            $table->boolean('is_cc_customer')->default(false);
            $table->boolean('is_cc_ssrr')->default(false);
            $table->boolean('is_cc_carrier')->default(false);
            $table->boolean('is_cc_consignee')->default(false);
            $table->boolean('is_cc_driver')->default(false);
            $table->boolean('is_cc_shipper')->default(false);
            $table->boolean('is_cc_vendor')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('t_addresses', function (Blueprint $table) {
            $table->dropColumnIfExists('is_cc_pickup');
            $table->dropColumnIfExists('is_cc_delivery');
            $table->dropColumnIfExists('is_cc_return');
            $table->dropColumnIfExists('is_cc_payor');
            $table->dropColumnIfExists('is_cc_customer');
            $table->dropColumnIfExists('is_cc_ssrr');
            $table->dropColumnIfExists('is_cc_broker');
            $table->dropColumnIfExists('is_cc_billto');
        });
    }
}
