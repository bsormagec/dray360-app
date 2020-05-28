<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TCreateAccountOCRVariantOCRRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_account_ocrvariant_ocrrules', function (Blueprint $table) {
            $table->bigIncrements('id', true, true);
            $table->bigInteger('t_account_id', false, true);
            $table->bigInteger('t_ocrvariant_id', false, true);
            $table->bigInteger('t_ocrrule_id', false, true);
            $table->integer('rule_sequence', false, true);
            $table->timestamps();
            $table->softDeletes();

            // add foreign key constraints
            $table->foreign('t_account_id')->references('id')->on('t_accounts');
            $table->foreign('t_ocrvariant_id')->references('id')->on('t_ocrvariants');
            $table->foreign('t_ocrrule_id')->references('id')->on('t_ocrrules');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_account_ocrvariant_ocrrules');
    }
}
