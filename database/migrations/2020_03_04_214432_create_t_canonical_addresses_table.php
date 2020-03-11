<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTCanonicalAddressesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_canonical_addresses', function (Blueprint $table) {
            $table->bigIncrements('id', true, true);
            $table->bigInteger('t_address_id', false, true);
            $table->bigInteger('t_contact_id', false, true);
            // $table->datetime('deleted_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // add foreign key constraints
            $table->foreign('t_address_id')->references('id')->on('t_addresses');
            $table->foreign('t_contact_id')->references('id')->on('t_contacts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('t_canonical_addresses');
    }
}
