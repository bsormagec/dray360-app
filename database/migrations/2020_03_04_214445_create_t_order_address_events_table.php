<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTOrderAddressEventsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_order_address_events', function (Blueprint $table) {
            $table->bigIncrements('id', true, true);
            $table->string('address_schedule_description', 512)->nullable();
            $table->bigInteger('t_order_id', false, true);
            $table->bigInteger('t_address_id', false, true);
            $table->integer('event_number', false, true);
            $table->boolean('is_hook_event')->nullable();
            $table->boolean('is_mount_event')->nullable();
            $table->boolean('is_deliver_event')->nullable();
            $table->boolean('is_dismount_event')->nullable();
            $table->boolean('is_drop_event')->nullable();
            $table->boolean('call_for_appointment')->nullable();
            $table->time('delivery_window_from_localtime')->nullable();
            $table->time('delivery_window_to_localtime')->nullable();
            $table->string('delivery_instructions', 4096)->nullable();
            // $table->datetime('deleted_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // add foreign key constraints
            $table->foreign('t_address_id')->references('id')->on('t_addresses');
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
        Schema::drop('t_order_address_events');
    }
}
