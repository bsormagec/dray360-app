<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTChainioRequestidSubmissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_chainio_requestid_submissions', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('request_id', 512)->index();
            $table->unsignedBigInteger('t_order_id')->index();

            // foreign key to order_id
            $table->foreign('t_order_id')->references('id')->on('t_orders');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_chainio_requestid_submissions');
    }
}
