<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddClassifierColumnsToTOcrVariants extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_ocrvariants', function (Blueprint $table) {
            $table->string('classifier', 128)->nullable();
            $table->string('parser', 128)->nullable();
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
            $table->dropColumnIfExists('classifier');
            $table->dropColumnIfExists('parser');
        });
    }
}
