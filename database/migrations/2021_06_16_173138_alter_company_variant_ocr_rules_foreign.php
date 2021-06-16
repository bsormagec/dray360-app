<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCompanyVariantOcrRulesForeign extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_company_ocrvariant_ocrrules', function (Blueprint $table) {
            $table->dropForeign('t_company_ocrvariant_ocrrules_t_company_id_foreign');
            $table->unsignedBigInteger('t_company_id')->nullable()->change();
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
            $table->unsignedBigInteger('t_company_id')->change();
            $table->foreign('t_company_id')->references('id')->on('t_companies');
        });
    }
}
