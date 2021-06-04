<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVirtualColumnForPdfOveragesInDailyMetrics extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_company_daily_metrics', function (Blueprint $table) {
            $table->integer('pdf_pages_overage')->virtualAs('greatest(0, pdf_orders_including_deleted - (2 * pdf_pages_including_deleted))');
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
            $table->dropColumnIfExists('pdf_pages_overage');
        });
    }
}
