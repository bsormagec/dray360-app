<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCutoffDatetimeToTOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_orders', function (Blueprint $table) {
            $table->datetime('cutoff_date')->nullable();  // yes, this column should be datetime(), not just date()
            $table->time('cutoff_time')->nullable();
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
            dropColumnIfExists($tableName, 'cutoff_date');
            dropColumnIfExists($tableName, 'cutoff_time');
        });
    }
}
