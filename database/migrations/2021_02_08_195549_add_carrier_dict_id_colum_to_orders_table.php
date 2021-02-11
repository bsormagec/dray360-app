<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCarrierDictIdColumToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_orders', function (Blueprint $table) {
            $table->unsignedBigInteger('carrier_dictid')->nullable();
            $table->boolean('carrier_dictid_verified')->nullable();

            $table->foreign('carrier_dictid')->references('id')->on('t_dictionary_items');
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
            $table->dropColumnIfExists('carrier_dictid_verified');
            $table->dropColumnIfExists('carrier_dictid');
        });
    }
}
