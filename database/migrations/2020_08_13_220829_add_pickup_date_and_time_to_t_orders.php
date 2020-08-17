<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPickupDateAndTimeToTOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_orders', function (Blueprint $table) {
            $table->datetime('pickup_by_date')->nullable();  # yes, this column should be datetime(), not just date()
            $table->time('pickup_by_time')->nullable();
        });

        // Drop expedite_shipment column in t_orders
        if (Schema::hasColumn('t_orders', 'expedite_shipment')) {
            Schema::table(
                't_orders',
                function (Blueprint $table) {
                    $table->dropColumn('expedite_shipment');
                }
            );
        }
    }



    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop pickup_by_date column in t_orders
        if (Schema::hasColumn('t_orders', 'pickup_by_date')) {
            Schema::table(
                't_orders',
                function (Blueprint $table) {
                    $table->dropColumn('pickup_by_date');
                }
            );
        }

        // Drop pickup_by_time column in t_orders
        if (Schema::hasColumn('t_orders', 'pickup_by_time')) {
            Schema::table(
                't_orders',
                function (Blueprint $table) {
                    $table->dropColumn('pickup_by_time');
                }
            );
        }

        // Replace expedite_shipment column in t_orders
        Schema::table('t_orders', function (Blueprint $table) {
            $table->boolean('expedite_shipment')->nullable();
        });
    }
}
