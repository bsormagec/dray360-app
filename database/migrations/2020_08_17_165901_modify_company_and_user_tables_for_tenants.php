<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyCompanyAndUserTablesForTenants extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_companies', function (Blueprint $table) {
            $table->json('configuration')->nullable();
            $table->unsignedBigInteger('t_domain_id')->nullable()->index();

            $table->foreign('t_domain_id')->references('id')->on('t_domains');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->json('configuration')->nullable();
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
            $table->dropForeign(['t_domain_id']);
            $table->dropColumn(['configuration', 't_domain_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['configuration']);
        });
    }
}
