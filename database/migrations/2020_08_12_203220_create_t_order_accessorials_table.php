<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTOrderAccessorialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_order_accessorials', function (Blueprint $table) {
            $table->id();
            $table->string('rulesngine_name');
            $table->bigInteger('t_order_id');
            $table->decimal('amount', 7, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('t_order_accessorials')) {
            Schema::dropIfExists('t_order_accessorials');
        }
    }
}
