<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCalculatedStoredColumnInAddressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_addresses', function (Blueprint $table) {
            $storedColumnQuery = <<<'mysql'
                lower(concat(
                    coalesce(location_name, ''),
                    coalesce(concat(' ', trim(address_line_1)), ''),
                    coalesce(concat(' ', trim(address_line_2)), ''),
                    coalesce(concat(' ', trim(city)), ''),
                    coalesce(concat(' ', trim(state)), ''),
                    coalesce(concat(' ', trim(postal_code)), ''),
                    coalesce(concat(' ', trim(county)), ''),
                    coalesce(concat(' ', trim(country)), '')
                ))
            mysql;
            $table->string('address_concatenated_text')->storedAs($storedColumnQuery);
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
            $table->dropColumn(['address_concatenated_text']);
        });
    }
}
