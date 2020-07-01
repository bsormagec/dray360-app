<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAddressTypeColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_addresses', function (Blueprint $table) {
            $table->boolean('is_billable')->nullable();
            $table->boolean('is_terminal')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('t_addresses', 'is_billable'))
        {
            Schema::table('t_addresses', function (Blueprint $table) {
                $table->dropColumn('is_billable');
            });
        }

        if (Schema::hasColumn('t_addresses', 'is_terminal'))
        {
            Schema::table('t_addresses', function (Blueprint $table) {
                $table->dropColumn('is_terminal');
            });
        }
    }
}
