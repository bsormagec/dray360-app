<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_addresses', function (Blueprint $table) {
            $table->bigIncrements('id', true, true);
            $table->decimal('latitude', 12, 8);
            $table->decimal('longitude', 12, 8);
            $table->string('address_line_1', 512)->nullable();
            $table->string('address_line_2', 512)->nullable();
            $table->string('city', 128)->nullable();
            $table->string('county', 64)->nullable();
            $table->string('state', 128)->nullable();
            $table->string('postal_code', 16)->nullable();
            $table->string('country', 128)->nullable();
            $table->string('hours_of_operation', 1024)->nullable();
            // $table->datetime('deleted_at')->nullable();
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
        Schema::drop('t_addresses');
    }
}
