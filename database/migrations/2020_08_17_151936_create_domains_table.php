<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDomainsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_domains', function (Blueprint $table) {
            $table->id();
            $table->string('description', 100)->nullable();
            $table->string('hostname', 60)->unique();
            $table->unsignedBigInteger('t_tenant_id')->index();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('t_tenant_id')->references('id')->on('t_tenants');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_domains');
    }
}
