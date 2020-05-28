<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_orders', function (Blueprint $table) {
            $table->bigIncrements('id', true, true);
            $table->string('request_id', 512)->nullable();
            $table->string('shipment_designation', 64)->nullable();
            $table->string('equipment_type', 64)->nullable();
            $table->string('shipment_direction', 64)->nullable();
            $table->boolean('one_way')->nullable();
            $table->boolean('yard_pre_pull')->nullable();
            $table->boolean('has_chassis')->nullable();
            $table->string('unit_number', 64)->nullable();
            $table->string('equipment', 64)->nullable();
            $table->string('equipment_size', 64)->nullable();
            $table->string('owner_or_ss_company', 64)->nullable();
            $table->boolean('hazardous')->nullable();
            $table->boolean('expedite_shipment')->nullable();
            $table->string('reference_number', 64)->nullable();
            $table->string('rate_quote_number', 64)->nullable();
            $table->text('seal_number_list')->nullable();
            $table->string('port_ramp_of_origin', 64)->nullable();
            $table->string('port_ramp_of_destination', 64)->nullable();
            $table->string('vessel', 64)->nullable();
            $table->string('voyage', 64)->nullable();
            $table->string('master_bol_mawb', 64)->nullable();
            $table->string('house_bol_hawb', 64)->nullable();
            $table->datetime('estimated_arrival_utc')->nullable();
            $table->datetime('last_free_date_utc')->nullable();
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
        Schema::drop('t_orders');
    }
}
