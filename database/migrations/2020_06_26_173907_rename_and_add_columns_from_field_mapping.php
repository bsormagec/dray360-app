<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameAndAddColumnsFromFieldMapping extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_orders', function (Blueprint $table) {
            $table->renameColumn('seal_number_list', 'seal_number');
            $table->renameColumn('bol', 'bill_of_lading');

            $table->string('carrier', 64)->nullable();
        });

        Schema::table('t_order_line_items', function (Blueprint $table) {
            $table->renameColumn('description', 'contents');
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
            $table->renameColumn('seal_number', 'seal_number_list');
            $table->renameColumn('bill_of_lading', 'bol');

            $table->dropColumn('carrier');
        });

        Schema::table('t_order_line_items', function (Blueprint $table) {
            $table->renameColumn('contents', 'description');
        });
    }
}
