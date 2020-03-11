<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTOrderLineItemsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_order_line_items', function (Blueprint $table) {
            $table->bigIncrements('id', true, true);
            $table->bigInteger('t_order_id', false, true);
            $table->integer('quantity', false, true);
            $table->string('unit_of_measure', 64)->nullable();
            $table->string('description', 4096)->nullable();
            $table->decimal('weight', 18, 6);
            $table->decimal('total_weight', 18, 6);
            $table->string('weight_uom', 64)->nullable();
            $table->boolean('is_hazardous')->nullable();
            $table->string('haz_contact_name', 128)->nullable();
            $table->string('haz_phone', 64)->nullable();
            $table->string('haz_un_code', 64)->nullable();
            $table->string('haz_un_name', 512)->nullable();
            $table->string('haz_class', 512)->nullable();
            $table->string('haz_qualifier', 64)->nullable();
            $table->string('haz_description', 4096)->nullable();
            $table->integer('haz_imdg_page_number', false);
            $table->integer('haz_flashpoint_temp', false);
            $table->string('haz_flashpoint_temp_uom', 16)->nullable();
            $table->string('packaging_group', 64)->nullable();
            // $table->datetime('deleted_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // add foreign key constraints
            $table->foreign('t_order_id')->references('id')->on('t_orders');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('t_order_line_items');
    }
}
