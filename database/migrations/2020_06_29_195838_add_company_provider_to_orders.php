<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCompanyProviderToOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_orders', function (Blueprint $table) {
            $table->unsignedBigInteger('t_company_id')->nullable();
            $table->renameColumn('t_tms_providers_id', 't_tms_provider_id');
        });

        Schema::table('t_companies', function (Blueprint $table) {
            $table->string('email_intake_address', 256)->index()->nullable();
            $table->string('email_intake_address_alt', 256)->index()->nullable();
            $table->unsignedBigInteger('default_tms_provider_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('t_orders', 't_tms_provider_id')) {
            Schema::table('t_orders', function (Blueprint $table) {
                $table->renameColumn('t_tms_provider_id', 't_tms_providers_id');
            });
        }

        if (Schema::hasColumn('t_orders', 't_company_id')) {
            Schema::table('t_orders', function (Blueprint $table) {
                $table->dropColumn('t_company_id');
            });
        }

        if (Schema::hasColumn('t_orders', 't_tms_provider_id')) {
            Schema::table('t_orders', function (Blueprint $table) {
                $table->dropColumn('t_tms_provider_id');
            });
        }

        if (Schema::hasColumn('t_companies', 'email_intake_address')) {
            Schema::table('t_companies', function (Blueprint $table) {
                $table->dropColumn('email_intake_address');
            });
        }

        if (Schema::hasColumn('t_companies', 'email_intake_address_alt')) {
            Schema::table('t_companies', function (Blueprint $table) {
                $table->dropColumn('email_intake_address_alt');
            });
        }

        if (Schema::hasColumn('t_companies', 'default_tms_provider_id')) {
            Schema::table('t_companies', function (Blueprint $table) {
                $table->dropColumn('default_tms_provider_id');
            });
        }
    }
}
