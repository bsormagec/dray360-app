<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLabelColumnsToTOcrvariants extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_ocrvariants', function (Blueprint $table) {
            $table->text('abbyy_label1', 256)->nullable();
            $table->text('abbyy_label2', 256)->nullable();
            $table->text('abbyy_label3', 256)->nullable();
            $table->text('abbyy_label4', 256)->nullable();
            $table->text('abbyy_label5', 256)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('t_ocrvariants', function (Blueprint $table) {
            $table->dropColumnIfExists('abbyy_label1');
            $table->dropColumnIfExists('abbyy_label2');
            $table->dropColumnIfExists('abbyy_label3');
            $table->dropColumnIfExists('abbyy_label4');
            $table->dropColumnIfExists('abbyy_label5');
        });
    }
}
