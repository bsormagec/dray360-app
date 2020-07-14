<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateOrderColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // add columns to t_orders table
        Schema::table('t_orders', function (Blueprint $table) {
            $table->string('variant_id', 32)->nullable();
            $table->string('variant_name', 256)->nullable();
            $table->unsignedBigInteger('t_tms_providers_id')->nullable();
            $table->string('tms_shipment_id', 64)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // drop columns from t_orders table
        Schema::table('t_orders', function (Blueprint $table) {
            $table->dropColumn('variant_id');
            $table->dropColumn('variant_name');
            $table->dropColumn('t_tms_providers_id');
            $table->dropColumn('tms_shipment_id');
        });
    }
}
