<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLineprefixToTEquipmentTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_equipment_types', function (Blueprint $table) {
            $table->json('line_prefix_list')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('t_equipment_types', function (Blueprint $table) {
            $table->dropColumnIfExists('line_prefix_list');
        });
    }
}
