<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeCompcareAuthColumnsInCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_companies', function (Blueprint $table) {
            $table->dropColumnIfExists('compcare_organization_id');
            $table->dropColumnIfExists('compcare_username');
            $table->dropColumnIfExists('compcare_password');

            $table->text('compcare_api_key')->nullable();
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
            $table->dropColumnIfExists('compcare_api_key');

            $table->string('compcare_organization_id', 16)->nullable();
            $table->string('compcare_username', 128)->nullable();
            $table->text('compcare_password')->nullable();
        });
    }
}
