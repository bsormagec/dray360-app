<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameAccountVariantRuleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_account_ocrvariant_ocrrules', function (Blueprint $table) {
            $table->dropForeign(['t_account_id']);
        });

        Schema::rename('t_account_ocrvariant_ocrrules', 't_company_ocrvariant_ocrrules');

        DB::table('t_company_ocrvariant_ocrrules')->update(['t_account_id' => 1]);

        Schema::table('t_company_ocrvariant_ocrrules', function (Blueprint $table) {
            $table->renameColumn('t_account_id', 't_company_id');
            $table->foreign('t_company_id')->references('id')->on('t_companies');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('t_company_ocrvariant_ocrrules', function (Blueprint $table) {
            $table->dropForeign(['t_company_id']);
            $table->renameColumn('t_company_id', 't_account_id');
        });

        DB::table('t_company_ocrvariant_ocrrules')->update(['t_account_id' => 8]);

        Schema::rename('t_company_ocrvariant_ocrrules', 't_account_ocrvariant_ocrrules');

        Schema::table('t_account_ocrvariant_ocrrules', function (Blueprint $table) {
            $table->foreign('t_account_id')->references('id')->on('t_accounts');
        });
    }
}
