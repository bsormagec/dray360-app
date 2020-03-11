<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTCompanyAddressTmsCodeTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_company_address_tms_code', function (Blueprint $table) {
            $table->bigIncrements('id', true, true);
            $table->bigInteger('t_address_id', false, true);
            $table->bigInteger('t_company_id', false, true);
            $table->bigInteger('t_tms_provider_id', false, true);
            $table->string('company_address_tms_code', 512)->nullable();
            // $table->datetime('deleted_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // add foreign key constraints
            $table->foreign('t_address_id')->references('id')->on('t_addresses');
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
        Schema::drop('t_company_address_tms_code');
    }
}
