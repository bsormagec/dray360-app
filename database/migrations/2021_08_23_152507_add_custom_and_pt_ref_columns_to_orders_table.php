<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCustomAndPtRefColumnsToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_orders', function (Blueprint $table) {
            $table->string('custom1', 256)->nullable();
            $table->string('custom2', 256)->nullable();
            $table->string('custom3', 256)->nullable();
            $table->string('custom4', 256)->nullable();
            $table->string('custom5', 256)->nullable();
            $table->string('custom6', 256)->nullable();
            $table->string('custom7', 256)->nullable();
            $table->string('custom8', 256)->nullable();
            $table->string('custom9', 256)->nullable();
            $table->string('custom10', 256)->nullable();
            $table->string('pt_ref1_text', 256)->nullable();
            $table->string('pt_ref2_text', 256)->nullable();
            $table->string('pt_ref3_text', 256)->nullable();
            $table->string('pt_ref1_type', 64)->nullable();
            $table->string('pt_ref2_type', 64)->nullable();
            $table->string('pt_ref3_type', 64)->nullable();
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
            $table->dropColumnIfExists('custom1');
            $table->dropColumnIfExists('custom2');
            $table->dropColumnIfExists('custom3');
            $table->dropColumnIfExists('custom4');
            $table->dropColumnIfExists('custom5');
            $table->dropColumnIfExists('custom6');
            $table->dropColumnIfExists('custom7');
            $table->dropColumnIfExists('custom8');
            $table->dropColumnIfExists('custom9');
            $table->dropColumnIfExists('custom10');
            $table->dropColumnIfExists('pt_ref1_text');
            $table->dropColumnIfExists('pt_ref2_text');
            $table->dropColumnIfExists('pt_ref3_text');
            $table->dropColumnIfExists('pt_ref1_type');
            $table->dropColumnIfExists('pt_ref2_type');
            $table->dropColumnIfExists('pt_ref3_type');
        });
    }
}
