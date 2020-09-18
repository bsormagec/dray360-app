<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEquipmentTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_equipment_types', function (Blueprint $table) {
            $storedColumnQuery = <<<'mysql'
                if(
                    row_type = 'combined',
                    equipment_type_and_size,
                    if(
                        row_type = 'separate',
                        concat(equipment_type, ' ',equipment_size),
                        ''
                    )
                )
            mysql;

            $table->id();
            $table->unsignedBigInteger('t_company_id');
            $table->unsignedBigInteger('t_tms_provider_id');
            $table->string('tms_equipment_id', 128);
            $table->string('equipment_owner', 128);
            $table->string('row_type', 16);
            $table->string('equipment_type_and_size')->nullable();
            $table->string('equipment_type')->nullable();
            $table->string('equipment_size')->nullable();
            $table->string('equipment_display')->storedAs($storedColumnQuery);
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('t_company_id')->references('id')->on('t_companies');
            $table->foreign('t_tms_provider_id')->references('id')->on('t_tms_providers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_equipment_types');
    }
}
