<?php

use App\Models\Company;
use App\Models\DictionaryItem;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableDictonaryItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_dictionary_items', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->unsignedBigInteger('t_company_id')->nullable()->index();
            $table->unsignedBigInteger('t_tms_provider_id')->nullable()->index();
            $table->unsignedBigInteger('t_user_id')->nullable()->index();

            $table->foreign('t_company_id')->references('id')->on('t_companies');
            $table->foreign('t_tms_provider_id')->references('id')->on('t_tms_providers');
            $table->foreign('t_user_id')->references('id')->on('users');

            $table->string('item_type', 64)->index();
            $table->string('item_key', 512)->index();
            $table->string('item_display_name', 128);

            $table->json('item_value')->nullable();

            $table->softDeletes();
        });

        Company::whereJsonLength('configuration->profit_tools_template_list', '>', 0)
            ->get()
            ->flatMap(function ($company) {
                return collect($company->configuration['profit_tools_template_list'])
                    ->map(fn ($templateItem) => $templateItem + ['company_id' => $company->id])
                    ->toArray();
            })
            ->each(function ($templateItem) {
                DictionaryItem::create([
                    't_company_id' => $templateItem['company_id'],
                    'item_type' => DictionaryItem::TEMPLATE_TYPE,
                    'item_key' => $templateItem['tms_template_id'],
                    'item_display_name' => $templateItem['tms_template_name'],
                ]);
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_dictionary_items');
    }
}
