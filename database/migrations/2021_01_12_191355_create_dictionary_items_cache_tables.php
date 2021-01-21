<?php

use App\Models\DictionaryItem;
use Illuminate\Support\Facades\Schema;
use App\Models\DictionaryCacheDefinition;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDictionaryItemsCacheTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_dictionary_cache_entries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('t_dictionary_item_id')->index();
            $table->foreign('t_dictionary_item_id')->references('id')->on('t_dictionary_items');

            $table->unsignedBigInteger('t_company_id')->index();
            $table->foreign('t_company_id')->references('id')->on('t_companies');

            $table->string('cache_type', 128);
            $table->unsignedInteger('verified_count')->default(0);

            $table->string('cached_variant_name', 512)->nullable();
            $table->text('cached_bill_to_address_raw_text')->nullable();
            $table->text('cached_event1_address_raw_text')->nullable();
            $table->text('cached_event2_address_raw_text')->nullable();

            $table->timestamps();
        });

        Schema::create('t_dictionary_cache_definitions', function (Blueprint $table) {
            $table->id();

            $table->string('cache_type', 128);

            $table->boolean('use_variant_name')->default(false);
            $table->boolean('use_bill_to_address_raw_text')->default(false);
            $table->boolean('use_event1_address_raw_text')->default(false);
            $table->boolean('use_event2_address_raw_text')->default(false);

            $table->timestamps();
        });

        DictionaryCacheDefinition::create([
            'cache_type' => DictionaryItem::TEMPLATE_TYPE,
            'use_variant_name' => true,
            'use_bill_to_address_raw_text' => true,
            'use_event1_address_raw_text' => true,
            'use_event2_address_raw_text' => true,
        ]);

        Schema::table('t_orders', function (Blueprint $table) {
            $table->boolean('tms_template_dictid_verified')->default(false);
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
            $table->dropColumnIfExists('tms_template_dictid_verified');
        });
        Schema::dropIfExists('t_dictionary_cache_definitions');
        Schema::dropIfExists('t_dictionary_cache_entries');
    }
}
