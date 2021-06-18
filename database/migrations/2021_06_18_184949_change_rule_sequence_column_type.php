<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeRuleSequenceColumnType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_company_ocrvariant_ocrrules', function (Blueprint $table) {
            $table->integer('rule_sequence')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('t_company_ocrvariant_ocrrules', function (Blueprint $table) {
            $table->unsignedInteger('rule_sequence')->nullable()->change();
        });
    }
}
