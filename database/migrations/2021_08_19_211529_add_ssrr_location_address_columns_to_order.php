<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSsrrLocationAddressColumnsToOrder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_orders', function (Blueprint $table) {
            $table->unsignedBigInteger('ssrr_location_address_id')->nullable();
            $table->boolean('ssrr_location_address_verified')->default(false)->nullable();
            $table->text('ssrr_location_address_raw_text')->nullable();

            $table->foreign('ssrr_location_address_id')->references('id')->on('t_addresses');
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
            $table->dropColumnIfExists('ssrr_location_address_id');
            $table->dropColumnIfExists('ssrr_location_address_verified');
            $table->dropColumnIfExists('ssrr_location_address_raw_text');
        });
    }
}
