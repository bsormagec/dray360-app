<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompanyDailyMetricTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_company_daily_metrics', function (Blueprint $table) {
            $table->id();
            $table->date('metric_date');
            $table->unsignedBigInteger('t_company_id');
            $table->timestamps();

            $table->integer('requests')->default(0);
            $table->integer('requests_all_updateprior')->default(0);
            $table->integer('requests_mixed_updateprior')->default(0);
            $table->integer('requests_none_updateprior')->default(0);
            $table->integer('pdf_requests')->default(0);
            $table->integer('pdf_requests_all_updateprior')->default(0);
            $table->integer('pdf_requests_mixed_updateprior')->default(0);
            $table->integer('pdf_requests_none_updateprior')->default(0);
            $table->integer('pdf_requests_singleorder')->default(0);
            $table->integer('pdf_requests_singleorder_all_updateprior')->default(0);
            $table->integer('pdf_requests_singleorder_mixed_updateprior')->default(0);
            $table->integer('pdf_requests_singleorder_none_updateprior')->default(0);
            $table->integer('pdf_requests_multiorder')->default(0);
            $table->integer('pdf_requests_multiorder_all_updateprior')->default(0);
            $table->integer('pdf_requests_multiorder_mixed_updateprior')->default(0);
            $table->integer('pdf_requests_multiorder_none_updateprior')->default(0);
            $table->integer('pdf_requests_multiorder_less_all_updateprior')->default(0);
            $table->integer('pdf_orders')->default(0);
            $table->integer('pdf_orders_updateprior')->default(0);
            $table->integer('pdf_orders_dontupdateprior')->default(0);
            $table->integer('pdf_orders_less_requests_anyupdateprior')->default(0);
            $table->integer('datafile_requests')->default(0);
            $table->integer('datafile_requests_all_updateprior')->default(0);
            $table->integer('datafile_requests_mixed_updateprior')->default(0);
            $table->integer('datafile_requests_none_updateprior')->default(0);
            $table->integer('datafile_orders')->default(0);
            $table->integer('datafile_orders_updateprior')->default(0);
            $table->integer('datafile_orders_dontupdateprior')->default(0);
            $table->integer('datafile_orders_less_requests_anyupdateprior')->default(0);
            $table->integer('orders_deleted')->default(0);
            $table->integer('pdf_orders_deleted')->default(0);
            $table->integer('datafile_orders_deleted')->default(0);
            $table->integer('requests_rejected')->default(0);
            $table->integer('pdf_pages_including_deleted')->default(0);
            $table->integer('tms_shipments')->default(0);
            $table->integer('requests_deleted')->default(0);
            $table->integer('pdf_requests_deleted')->default(0);
            $table->integer('datafile_requests_deleted')->default(0);
            $table->integer('pdf_orders_including_deleted')->default(0);
            $table->integer('datafile_orders_including_deleted')->default(0);

            $table->foreign('t_company_id')->references('id')->on('t_companies');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_company_daily_metrics');
    }
}
