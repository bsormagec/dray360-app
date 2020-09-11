<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TCompanyOcrvariantAccessorialMappings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_company_ocrvariant_accessorial_mappings', function (Blueprint $table) {
            $table->json('mapping')->nullable();
            $table->unsignedBigInteger('t_company_id');
            $table->unsignedBigInteger('t_ocrvariant_id');
            $table->foreign('t_company_id', 'accesorialmappings_company_foreign')->references('id')->on('t_companies');
            $table->foreign('t_ocrvariant_id', 'accesorial_ocrvariant_foreign')->references('id')->on('t_ocrvariants');

            $table->primary(['t_company_id', 't_ocrvariant_id'], 'company_variant_accessorial_primary_key');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('t_company_ocrvariant_accessorial_mappings');
    }
}
