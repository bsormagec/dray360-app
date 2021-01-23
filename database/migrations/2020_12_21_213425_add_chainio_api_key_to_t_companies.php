<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddChainioApiKeyToTCompanies extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_companies', function (Blueprint $table) {
            $table->text('chainio_url')->nullable();
            $table->text('chainio_api_key')->nullable();
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
            $table->dropColumnIfExists('chainio_url');
            $table->dropColumnIfExists('chainio_api_key');
        });
    }
}
