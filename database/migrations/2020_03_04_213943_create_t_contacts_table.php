<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTContactsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_contacts', function (Blueprint $table) {
            $table->bigIncrements('id', true, true);
            $table->bigInteger('t_company_id', false, true);
            $table->bigInteger('t_address_id', false, true);
            $table->string('title', 64)->nullable();
            $table->string('first_name', 64)->nullable();
            $table->string('last_name', 64)->nullable();
            $table->string('phone1_number', 128)->nullable();
            $table->string('phone1_ext', 16)->nullable();
            $table->string('phone1_number_type', 32)->nullable();
            $table->string('phone2_number', 128)->nullable();
            $table->string('phone2_ext', 16)->nullable();
            $table->string('phone2_number_type', 32)->nullable();
            $table->string('phone3_number', 128)->nullable();
            $table->string('phone3_ext', 16)->nullable();
            $table->string('phone3_number_type', 32)->nullable();
            $table->string('email', 256)->nullable();
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
        Schema::drop('t_contacts');
    }
}
