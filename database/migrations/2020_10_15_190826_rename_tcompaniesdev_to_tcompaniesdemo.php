<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameTcompaniesdevToTcompaniesDemo extends Migration
{
    const UPDATE_TCOMPANIESDEV = <<<ENDOFSQL
        update t_companies set
             name = 'TCompaniesDemo'
            ,email_intake_address=concat('demo-', email_intake_address)
        where id=2
          and name = 'TCompaniesDev'
        ;
    ENDOFSQL;

    const REVERT_TCOMPANIESDEV = <<<ENDOFSQL
        update t_companies set
             name = 'TCompaniesDev'
            ,email_intake_address=replace(email_intake_address, 'demo-', '')
        where id=2
          and name = 'TCompaniesDemo'
        ;
    ENDOFSQL;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement(RenameTcompaniesdevToTcompaniesDemo::UPDATE_TCOMPANIESDEV);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement(RenameTcompaniesdevToTcompaniesDemo::REVERT_TCOMPANIESDEV);
    }
}
