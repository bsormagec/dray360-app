<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDatafileSupportToTOcrvariants extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_ocrvariants', function (Blueprint $table) {
            $table->string('variant_type', 16)->default('ocr')->nullable();   // valid values are: NULL (implies ocr), "ocr", "edi", and "tabular"
            $table->json('classification')->nullable(); // (json: required_header_field_list, forbidden_header_field_list)
            $table->json('mapping')->nullable(); // (json: list of header=fieldname, list of header=fixedvalue)
            $table->json('company_id_list')->nullable(); // (json: list of t_company.id, null implies all companies)
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
            $table->dropColumnIfExists('variant_type');
            $table->dropColumnIfExists('classification');
            $table->dropColumnIfExists('company_id_list');
            $table->dropColumnIfExists('mapping');
        });
    }

}
