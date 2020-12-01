<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIspickupeventToTOrderAddressEvents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasColumn('t_order_address_events', 'is_pickup_event')) {
            Schema::table('t_order_address_events', function (Blueprint $table) {
                $table->boolean('is_pickup_event')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop is_pickup_event column in t_orders
        if (Schema::hasColumn('t_order_address_events', 'is_pickup_event')) {
            Schema::table(
                't_order_address_events',
                function (Blueprint $table) {
                    $table->dropColumn('is_pickup_event');
                }
            );
        }
    }
}
