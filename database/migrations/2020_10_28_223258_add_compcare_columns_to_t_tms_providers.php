<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCompcareColumnsToTTmsProviders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(
            't_companies',
            function (Blueprint $table) {
                $table->string('compcare_organization_id', 16)->nullable();
                $table->string('compcare_username', 128)->nullable();
                $table->json('compcare_password')->nullable();
                $table->json('compcare_token')->nullable();
            }
        );

        DB::table('t_tms_providers')->insert(
            [
                'id' => 2,
                'name' => 'Compcare',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(
            't_companies',
            function (Blueprint $table) {
                $table->dropColumnIfExists('compcare_organization_id');
                $table->dropColumnIfExists('compcare_username');
                $table->dropColumnIfExists('compcare_password');
                $table->dropColumnIfExists('compcare_token');
            }
        );

        DB::table('t_tms_providers')
            ->where('id', '=', 2)
            ->delete();
    }
}
