<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTDivisionCodeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_division_codes', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('t_company_id')->unsigned();
            $table->foreign('t_company_id')->references('id')->on('t_companies');
            $table->bigInteger('t_tms_provider_id')->unsigned();
            $table->foreign('t_tms_provider_id')->references('id')->on('t_tms_providers');
            $table->string('division_code', 32);
            $table->string('division_name', 128);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
<<<<<<< HEAD
        Schema::dropIfExists('t_division_codes');
=======
        Schema::dropIfExists('t_division_code');
>>>>>>> add division code table, model and seeder
    }
}
