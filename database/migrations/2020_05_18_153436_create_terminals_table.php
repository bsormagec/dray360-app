<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


/*
note: signature for $table->bigInteger('bill_to_address_id', false, true)
has as the two booleans, the meaning:
public function bigInteger($column, $autoIncrement = false, $unsigned = false)
but is better to use: $table->unsignedBigInteger('bill_to_address_id', true)
*/





class CreateTerminalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_terminals', function (Blueprint $table) {
            $table->unsignedBigInteger('id', true);
            $table->unsignedBigInteger('t_address_id')->nullable();

            $table->string('verification_status', 64)->nullable();
            $table->string('name', 512)->nullable;
            $table->string('type', 32)->nullable();
            $table->string('terminal_code', 64)->nullable();
            $table->string('major_metro', 64)->nullable();
            $table->string('metro_region', 64)->nullable();
            $table->string('phone', 64)->nullable();
            $table->boolean('reviewed')->nullable();
            $table->boolean('address_verified')->nullable();
            $table->string('terminal_key', 32)->nullable();
            $table->string('firms_code', 16)->nullable();
            $table->boolean('is_depot')->nullable();

            $table->timestamps(); // created_at and updated_at
            $table->softDeletes(); // deleted_at
        });

        // add columns to t_addresses table
        Schema::table('t_addresses', function (Blueprint $table) {
            $table->string('location_phone', 128)->nullable();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_terminals');

        // drop columns from t_addresses table
        Schema::table('t_addresses', function (Blueprint $table) {
            $table->dropColumn('location_phone');
        });
    }
}
