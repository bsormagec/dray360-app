<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixCompcareColumnsInCompanieTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_companies', function (Blueprint $table) {
            $table->dropColumnIfExists('compcare_password');
            $table->dropColumnIfExists('compcare_token');
        });

        Schema::table('t_companies', function (Blueprint $table) {
            $table->text('compcare_password')->nullable();
            $table->text('compcare_token')->nullable();
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
            $table->dropColumnIfExists('compcare_password');
            $table->dropColumnIfExists('compcare_token');
        });

        Schema::table('t_companies', function (Blueprint $table) {
            $table->json('compcare_password')->nullable();
            $table->json('compcare_token')->nullable();
        });
    }
}
