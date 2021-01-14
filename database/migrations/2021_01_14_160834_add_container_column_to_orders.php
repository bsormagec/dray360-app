<?php

use App\Models\Company;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddContainerColumnToOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_orders', function (Blueprint $table) {
            $table->unsignedBigInteger('container_dictid')->nullable();

            $table->foreign('container_dictid')->references('id')->on('t_dictionary_items');
        });

        Company::where('name', 'like', 'ITG%')
            ->get()
            ->each(function ($company) {
                $configuration = $company->configuration;
                $configuration['itg_enable_containers'] = true;

                $company->update(['configuration' => $configuration]);
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('t_orders', function (Blueprint $table) {
            $table->dropColumnIfExists('container_dictid');
        });
    }
}
