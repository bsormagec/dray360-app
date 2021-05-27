<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOrderColumnToCompanyDailyMetrics extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_company_daily_metrics', function (Blueprint $table) {
            $table->integer('orders')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('t_company_daily_metrics', function (Blueprint $table) {
            $table->dropColumnIfExists('orders');
        });
    }
}
