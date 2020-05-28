<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TCreateOcrvariantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_ocrvariants', function (Blueprint $table) {
            $table->bigIncrements('id', true, true);
            $table->string('abbyy_variant_id', 64);
            $table->string('abbyy_variant_name', 512);
            $table->string('description', 1024);
            $table->timestamps();
            $table->softDeletes();

            // add indexes
            $table->index('abbyy_variant_id');
            $table->index('abbyy_variant_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_ocrvariants');
    }
}
