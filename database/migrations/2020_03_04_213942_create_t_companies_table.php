<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_companies', function (Blueprint $table) {
            $table->bigIncrements('id', true, true);
            $table->bigInteger('t_address_id', false, true);
            $table->string('name', 512)->nullable();
            // $table->datetime('deleted_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // add foreign key constraints
            $table->foreign('t_address_id')->references('id')->on('t_addresses');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('t_companies');
    }
}
