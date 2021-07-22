<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTermDivDictionaryItemColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_orders', function (Blueprint $table) {
            $table->string('termdiv', 16)->nullable();
            $table->unsignedBigInteger('termdiv_dictid')->nullable();
            $table->boolean('termdiv_dictid_verified')->nullable();

            $table->foreign('termdiv_dictid')->references('id')->on('t_dictionary_items');
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
            $table->dropColumnIfExists('termdiv');
            $table->dropColumnIfExists('termdiv_dictid_verified');
            $table->dropColumnIfExists('termdiv_dictid');
        });
    }
}
