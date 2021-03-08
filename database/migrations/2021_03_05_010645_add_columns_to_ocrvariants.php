<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToOcrvariants extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_ocrvariants', function (Blueprint $table) {
            $table->json('search_tags_list')->nullable();
            $table->json('excluded_fields_list')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('t_ocrvariants', function (Blueprint $table) {
            $table->dropColumnIfExists('search_tags_list');
            $table->dropColumnIfExists('excluded_fields_list');
        });
    }
}
