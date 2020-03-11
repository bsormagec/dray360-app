<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTCanonicalAddressMatchesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_canonical_address_matches', function (Blueprint $table) {
            $table->bigIncrements('id', true, true);
            $table->bigInteger('t_address_id', false, true);
            $table->bigInteger('t_canonical_address_id', false, true);
            // $table->datetime('deleted_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // add foreign key constraints
            $table->foreign('t_address_id')->references('id')->on('t_addresses');
            $table->foreign('t_canonical_address_id')->references('id')->on('t_canonical_addresses');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('t_canonical_address_matches');
    }
}
