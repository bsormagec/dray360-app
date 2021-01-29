<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOcrAddressRawTextIndexToTVerifiedAddresses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_verified_addresses', function (Blueprint $table) {
            $table->index([DB::raw('ocr_address_raw_text(768)')]); # must index the first <n> characters
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('t_verified_addresses', function (Blueprint $table) {
            $table->dropIndex([DB::raw('ocr_address_raw_text(768)')]);
        });
    }
}
