<?php


// hand coded rollback (needed while getting the code-based version to work)
/*
alter table t_tms_providers drop foreign key t_tms_providers_t_fieldmap_id_foreign;
alter table t_tms_providers drop column t_fieldmap_id;

alter table t_companies drop foreign key t_companies_t_fieldmap_id_foreign;
alter table t_companies drop column t_fieldmap_id;

alter table t_ocrvariants drop foreign key t_companies_t_fieldmap_id_foreign;
alter table t_ocrvariants drop column t_fieldmap_id;

delete from migrations where migration = '2021_04_20_193247_create_t_fieldmaps_table';

drop table t_company_ocrvariant;
drop table t_fieldmaps;
*/


use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTfieldmapsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /////////////////////////////////
        // new t_fieldmaps table
        Schema::create('t_fieldmaps', function (Blueprint $table) {
            $table->id();
            $table->boolean('system_default')->default(false);
            $table->json('fieldmap_config')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('replaced_at')->nullable();
            $table->bigInteger('replacedby_id')->unsigned()->nullable();
            $table->bigInteger('replaces_id')->unsigned()->nullable();
        });

        // add self-referencing "foreign" keys to t_fieldmaps
        Schema::table('t_fieldmaps', function (Blueprint $table) {
            $table->foreign('replacedby_id')->references('id')->on('t_fieldmaps');
            $table->foreign('replaces_id')->references('id')->on('t_fieldmaps');
        });

        /////////////////////////////////
        // new t_company_ocrvariant table
        Schema::create('t_company_ocrvariant', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->bigInteger('t_company_id')->unsigned();
            $table->bigInteger('t_ocrvariant_id')->unsigned();
            $table->bigInteger('t_fieldmap_id')->unsigned();
        });

        // foreign keys
        Schema::table('t_company_ocrvariant', function (Blueprint $table) {
            $table->foreign('t_fieldmap_id')->references('id')->on('t_fieldmaps');
        });

        /////////////////////////////////
        // Update existing tables

        // t_ocrvariants
        Schema::table('t_ocrvariants', function (Blueprint $table) {
            $table->bigInteger('t_fieldmap_id')->unsigned()->nullable();
        });
        Schema::table('t_ocrvariants', function (Blueprint $table) {
            $table->foreign('t_fieldmap_id')->references('id')->on('t_fieldmaps');
        });

        // t_companies
        Schema::table('t_companies', function (Blueprint $table) {
            $table->bigInteger('t_fieldmap_id')->unsigned()->nullable();
        });
        Schema::table('t_companies', function (Blueprint $table) {
            $table->foreign('t_fieldmap_id')->references('id')->on('t_fieldmaps');
        });

        // t_tms_providers
        Schema::table('t_tms_providers', function (Blueprint $table) {
            $table->bigInteger('t_fieldmap_id')->unsigned()->nullable();
        });
        Schema::table('t_tms_providers', function (Blueprint $table) {
            $table->foreign('t_fieldmap_id')->references('id')->on('t_fieldmaps');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // t_tms_providers
        Schema::table('t_tms_providers', function (Blueprint $table) {
            $table->dropForeign(['t_fieldmap_id']);
        });
        Schema::table('t_tms_providers', function (Blueprint $table) {
            $table->dropColumnIfExists('t_fieldmap_id');
        });

        // t_companies
        Schema::table('t_companies', function (Blueprint $table) {
            $table->dropForeign(['t_fieldmap_id']);
        });
        Schema::table('t_companies', function (Blueprint $table) {
            $table->dropColumnIfExists('t_fieldmap_id');
        });

        // t_ocrvariants
        Schema::table('t_ocrvariants', function (Blueprint $table) {
            $table->dropForeign(['t_fieldmap_id']);
        });
        Schema::table('t_ocrvariants', function (Blueprint $table) {
            $table->dropColumnIfExists('t_fieldmap_id');
        });

        // t_company_ocrvariant (drop foreign key constraint)
        Schema::table('t_company_ocrvariant', function (Blueprint $table) {
            $table->dropForeign(['t_fieldmap_id']);
        });

        // drop new tables
        Schema::dropIfExists('t_company_ocrvariant');
        Schema::dropIfExists('t_fieldmaps');
    }
}
