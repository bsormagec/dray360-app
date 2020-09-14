<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVerifiedAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_verified_addresses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('t_company_id');
            $table->unsignedBigInteger('t_tms_provider_id');
            $table->text('ocr_address_raw_text')->nullable();
            $table->string('company_address_tms_code', 512)->nullable();
            $table->text('company_address_tms_text')->nullable();
            $table->unsignedInteger('verified_count')->default(0);
            $table->boolean('skip_verification')->default(false);
            $table->timestamps();
            $table->softDeletes();
            $table->text('deleted_reason')->nullable();

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
        Schema::dropIfExists('t_verified_addresses');
    }
}
