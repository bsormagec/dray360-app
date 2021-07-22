<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCcContainertypeDictionaryItemColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_orders', function (Blueprint $table) {
            $table->string('cc_containertype', 16)->nullable();
            $table->unsignedBigInteger('cc_containertype_dictid')->nullable();
            $table->boolean('cc_containertype_dictid_verified')->nullable();

            $table->foreign('cc_containertype_dictid')->references('id')->on('t_dictionary_items');
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
            $table->dropColumnIfExists('cc_containertype');
            $table->dropColumnIfExists('cc_containertype_dictid_verified');
            $table->dropColumnIfExists('cc_containertype_dictid');
        });
    }
}
