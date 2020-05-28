<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPickupNumberColumn extends Migration
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
            $table->string('pickup_number', 512)->nullable()->index();
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
            $table->dropColumn('pickup_number');
        });
    }
}
