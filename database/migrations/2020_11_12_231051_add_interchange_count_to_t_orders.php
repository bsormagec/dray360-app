<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInterchangeCountToTOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_orders', function (Blueprint $table) {
            $table->unsignedInteger('interchange_count')->nullable();
            $table->unsignedInteger('interchange_err_count')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('t_orders', function (Blueprint $table) {
            $table->dropColumnIfExists('interchange_count');
            $table->dropColumnIfExists('interchange_err_count');
        });
    }
}
