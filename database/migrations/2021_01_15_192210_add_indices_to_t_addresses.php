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
            $table->dropIndex('t_addresses_location_name_index');
            $table->dropIndex('t_addresses_city_index');
            $table->dropIndex('t_addresses_postal_code_index');
            $table->dropIndex('t_addresses_address_line_1_index');
            $table->dropIndex('t_addresses_address_line_2_index');
            $table->dropIndex('t_addresses_state_index');
        });
    }
}
