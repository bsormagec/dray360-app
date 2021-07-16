<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCompcareOrderStatusColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_orders', function (Blueprint $table) {
            $table->string('cc_orderstatus', 16)->nullable();
            $table->unsignedBigInteger('cc_orderstatus_dictid')->nullable();
            $table->boolean('cc_orderstatus_dictid_verified')->nullable();

            $table->foreign('cc_orderstatus_dictid')->references('id')->on('t_dictionary_items');
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
            $table->dropColumnIfExists('cc_orderstatus');
            $table->dropColumnIfExists('cc_orderstatus_dictid_verified');
            $table->dropColumnIfExists('cc_orderstatus_dictid');
        });
    }
}
