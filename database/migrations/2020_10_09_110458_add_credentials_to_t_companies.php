<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCredentialsToTCompanies extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_companies', function (Blueprint $table) {
            $table->text('blackfly_token')->nullable();
            $table->string('blackfly_imagetype', 64)->nullable();
            $table->string('ripcms_username', 128)->nullable();
            $table->text('ripcms_password')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('t_companies', 'blackfly_token')) {
            Schema::table('t_companies', function (Blueprint $table) {
                $table->dropColumn('blackfly_token');
            });
        }
        if (Schema::hasColumn('t_companies', 'blackfly_imagetype')) {
            Schema::table('t_companies', function (Blueprint $table) {
                $table->dropColumn('blackfly_imagetype');
            });
        }
        if (Schema::hasColumn('t_companies', 'ripcms_username')) {
            Schema::table('t_companies', function (Blueprint $table) {
                $table->dropColumn('ripcms_username');
            });
        }
        if (Schema::hasColumn('t_companies', 'ripcms_password')) {
            Schema::table('t_companies', function (Blueprint $table) {
                $table->dropColumn('ripcms_password');
            });
        }

    }
}
