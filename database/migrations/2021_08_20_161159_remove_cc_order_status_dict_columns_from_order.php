<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveCcOrderStatusDictColumnsFromOrder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_orders', function (Blueprint $table) {
            $table->dropColumnIfExists('cc_orderstatus_dictid');
            $table->dropColumnIfExists('cc_orderstatus_dictid_verified');
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
            $table->unsignedBigInteger('cc_orderstatus_dictid')->nullable();
            $table->boolean('cc_orderstatus_dictid_verified')->nullable()->default(false);
        });
    }
}
