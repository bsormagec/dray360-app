<?php

use App\Models\TMSProvider;
use App\Models\DictionaryItem;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCompcareLoadedEmptyColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_orders', function (Blueprint $table) {
            $table->string('cc_loadedempty', 16)->nullable();
            $table->unsignedBigInteger('cc_loadedempty_dictid')->nullable();
            $table->boolean('cc_loadedempty_dictid_verified')->nullable();

            $table->foreign('cc_loadedempty_dictid')->references('id')->on('t_dictionary_items');
        });

        if (app()->environment('testing') || app()->environment('test')) {
            return;
        }

        $compcare = TMSProvider::where('name', TMSProvider::COMPCARE)->first();

        DictionaryItem::create([
            't_tms_provider_id' => $compcare->id,
            'item_type' => DictionaryItem::CC_LOADEDEMPTY_TYPE,
            'item_key' => 'LD',
            'item_display_name' => 'LD',
        ]);
        DictionaryItem::create([
            't_tms_provider_id' => $compcare->id,
            'item_type' => DictionaryItem::CC_LOADEDEMPTY_TYPE,
            'item_key' => 'MT',
            'item_display_name' => 'MT',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('t_orders', function (Blueprint $table) {
            $table->dropColumnIfExists('cc_loadedempty');
            $table->dropColumnIfExists('cc_loadedempty_dictid_verified');
            $table->dropColumnIfExists('cc_loadedempty_dictid');
        });

        DictionaryItem::query()
            ->where('item_type', DictionaryItem::CC_LOADEDEMPTY_TYPE)
            ->delete();
    }
}
