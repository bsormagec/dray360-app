<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndicesToTAddresses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_addresses', function (Blueprint $table) {
            $table->index('location_name');
            $table->index('city');
            $table->index('postal_code');
            $table->index('address_line_1');
            $table->index('address_line_2');
            $table->index('state');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('t_addresses', function (Blueprint $table) {
            $table->dropIndex(['location_name']);
            $table->dropIndex(['city']);
            $table->dropIndex(['postal_code']);
            $table->dropIndex(['address_line_1']);
            $table->dropIndex(['address_line_2']);
            $table->dropIndex(['state']);
        });
    }
}
