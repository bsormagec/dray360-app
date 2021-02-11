<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddParserColumnsToTOcrvariants extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_ocrvariants', function (Blueprint $table) {
            $table->json('parser_options')->nullable();
            $table->json('parser_fields_list')->nullable();
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
            $table->dropColumnIfExists('parser_options');
            $table->dropColumnIfExists('parser_fields_list');
        });
    }
}
