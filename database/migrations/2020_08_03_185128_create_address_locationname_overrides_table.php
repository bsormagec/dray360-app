<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddressLocationnameOverridesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_address_locationname_overrides', function (Blueprint $table) {
            $table->bigIncrements('id', true, true);
            $table->bigInteger('t_tms_provider_id', false, true);
            $table->bigInteger('t_company_id', false, true);

            $table->string('location_name', 512)->nullable();
            $table->string('override_name', 512)->nullable();

            $table->timestamps();
            $table->softDeletes();

            // add foreign key constraints
            $table->foreign('t_tms_provider_id')->references('id')->on('t_tms_providers');
            $table->foreign('t_company_id')->references('id')->on('t_companies');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_address_locationname_overrides');
    }
}
