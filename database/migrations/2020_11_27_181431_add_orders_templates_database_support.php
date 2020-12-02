<?php

use App\Models\Company;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOrdersTemplatesDatabaseSupport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_orders', function (Blueprint $table) {
            $table->string('tms_template_id', 64)->nullable();
        });

        Company::query()
            ->whereIn('id', [6, 7])
            ->get()
            ->each(function (Company $company) {
                $configuration = $company->configuration ?? [];
                $configuration['profit_tools_enable_templates'] = true;
                $configuration['profit_tools_template_list'] = [
                    ['tms_template_name' => 'some_name', 'tms_template_id' => '123453'], //Actual values wil be provided by Heather Maynard
                ];
                $company->configuration = $configuration;
                $company->save();
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
            $table->dropColumnIfExists('tms_template_id');
        });

        Company::query()
            ->whereIn('id', [6, 7])
            ->get()
            ->each(function (Company $company) {
                $configuration = $company->configuration ?? [];
                unset($configuration['profit_tools_enable_templates']);
                unset($configuration['profit_tools_template_list']);
                $company->configuration = $configuration;
                $company->save();
            });
    }
}
