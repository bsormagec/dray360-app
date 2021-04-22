<?php


// hand coded rollback (needed while getting the code-based version to work)
/*
alter table t_tms_providers drop foreign key t_tms_providers_t_fieldmap_id_foreign;
alter table t_tms_providers drop column t_fieldmap_id;

alter table t_companies drop foreign key t_companies_t_fieldmap_id_foreign;
alter table t_companies drop column t_fieldmap_id;

delete from migrations where migration = '2021_04_20_193247_create_t_fieldmaps_table';

drop table t_company_ocrvariant; 
drop table t_fieldmaps; 
*/


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
        Schema::table('t_companies', function (Blueprint $table) {
            $table->bigInteger('t_fieldmap_id')->unsigned()->nullable();
        });

        Schema::table('t_companies', function (Blueprint $table) {
            $table->foreign('t_fieldmap_id')->references('id')->on('t_fieldmaps');
        });

        // add foreign keys
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
        Schema::table('t_tms_providers', function (Blueprint $table) {
            $table->dropForeign(['t_fieldmap_id']);
        });
        Schema::table('t_tms_providers', function (Blueprint $table) {
            $table->dropColumnIfExists('t_fieldmap_id');
        });

        Schema::table('t_companies', function (Blueprint $table) {
            $table->dropForeign(['t_fieldmap_id']);
        });
        Schema::table('t_companies', function (Blueprint $table) {
            $table->dropColumnIfExists('t_fieldmap_id');
        });

        Schema::table('t_company_ocrvariant', function (Blueprint $table) {
            $table->dropForeign(['t_fieldmap_id']);
        });

        Schema::dropIfExists('t_company_ocrvariant');
        Schema::dropIfExists('t_fieldmaps');

    }
}
