<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndicesToTDictionaryCacheEntries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add new column to t_orders
        Schema::table('t_orders', function (Blueprint $table) {
            $table->boolean('container_dictid_verified')->nullable();
        });

        // Add indices to existing columns in t_dictionary_cache_entries
        Schema::table('t_dictionary_cache_entries', function (Blueprint $table) {
            $table->index('cached_variant_name');
            $table->index([DB::raw('cached_bill_to_address_raw_text(768)')], 'cached_bill_to_address_index');  # must index the first <n> characters of "text" columns
            $table->index([DB::raw('cached_event1_address_raw_text(768)')], 'cached_event1_address_index'); # also must give different name, to avoid "Identifier name '..._index' is too long"
            $table->index([DB::raw('cached_event2_address_raw_text(768)')], 'cached_event2_address_index');
        });

        # Add new columns (with indices) to t_dictionary_cache_entries
        Schema::table('t_dictionary_cache_entries', function (Blueprint $table) {
            $table->boolean('cached_hazardous')->nullable()->index();
            $table->string('cached_equipment_size', 64)->nullable()->index();
            $table->string('cached_vessel', 64)->nullable()->index();
            $table->string('cached_carrier', 64)->nullable()->index();
            $table->string('cached_shipment_direction', 64)->nullable()->index();
            $table->text('cached_event3_address_raw_text')->nullable(); # index added below
            $table->string('cached_template_key', 512)->nullable()->index();
        });

        // Add new columns (with indices) to t_dictionary_cache_definitions
        Schema::table('t_dictionary_cache_definitions', function (Blueprint $table) {
            $table->boolean('use_hazardous')->nullable();
            $table->boolean('use_equipment_size')->nullable();
            $table->boolean('use_vessel')->nullable();
            $table->boolean('use_carrier')->nullable();
            $table->boolean('use_shipment_direction')->nullable();
            $table->boolean('use_event3_address_raw_text')->nullable();
            $table->boolean('use_template_key')->nullable();
        });

        // Add special index to newly created column in t_dictionary_cache_entries
        Schema::table('t_dictionary_cache_entries', function (Blueprint $table) {
            $table->index([DB::raw('cached_event3_address_raw_text(768)')], 'cached_event3_address_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // drop new column from t_orders
        Schema::table('t_orders', function (Blueprint $table) {
            $table->dropColumnIfExists('container_dictid_verified');
        });
        
        // drop new indices from t_dictionary_cache_entries
        Schema::table('t_dictionary_cache_entries', function (Blueprint $table) {
            $table->dropIndex(['cached_variant_name']);
            $table->dropIndex('cached_bill_to_address_index'); # notice not using [] because
            $table->dropIndex('cached_event1_address_index'); # this index name is precisely what was
            $table->dropIndex('cached_event2_address_index'); # specified above
        });

        # drop new columns from t_dictionary_cache_entries
        Schema::table('t_dictionary_cache_entries', function (Blueprint $table) {
            $table->dropColumnIfExists('cached_hazardous');
            $table->dropColumnIfExists('cached_equipment_size');
            $table->dropColumnIfExists('cached_vessel');
            $table->dropColumnIfExists('cached_carrier');
            $table->dropColumnIfExists('cached_shipment_direction');
            $table->dropColumnIfExists('cached_event3_address_raw_text');
            $table->dropColumnIfExists('cached_template_key');
        });

        // Remove new columns (and their indices) from t_dictionary_cache_definitions
        Schema::table('t_dictionary_cache_definitions', function (Blueprint $table) {
            $table->dropColumnIfExists('use_hazardous');
            $table->dropColumnIfExists('use_equipment_size');
            $table->dropColumnIfExists('use_vessel');
            $table->dropColumnIfExists('use_carrier');
            $table->dropColumnIfExists('use_shipment_direction');
            $table->dropColumnIfExists('use_event3_address_raw_text');
            $table->dropColumnIfExists('use_template_key');
        });
    }
}
