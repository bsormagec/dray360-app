<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVesselDictIdColumnToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_orders', function (Blueprint $table) {
            $table->unsignedBigInteger('vessel_dictid')->nullable();
            $table->boolean('vessel_dictid_verified')->nullable();

            $table->foreign('vessel_dictid')->references('id')->on('t_dictionary_items');
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
            $table->dropColumnIfExists('vessel_dictid_verified');
            $table->dropColumnIfExists('vessel_dictid');
        });
    }
}
