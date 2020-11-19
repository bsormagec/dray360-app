<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSyncAddressBooleanToCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_companies', function (Blueprint $table) {
            $table->boolean('sync_addresses')->default(0);
            $table->dropColumnIfExists('compcare_token');
            $table->unsignedBigInteger('t_address_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('t_companies', function (Blueprint $table) {
            $table->dropColumnIfExists('sync_addresses');
            $table->text('compcare_token')->nullable();
            $table->unsignedBigInteger('t_address_id')->change();
        });
    }
}
