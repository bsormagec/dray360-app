<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMultilineContentsToTOrderLineItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_order_line_items', function (Blueprint $table) {
            $table->text('contents')->nullable()->change();
            $table->text('multiline_contents')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('t_order_line_items', function (Blueprint $table) {
            $table->string('contents', 4096)->nullable()->change();
            $table->dropColumnIfExists('multiline_contents');
        });
    }
}
