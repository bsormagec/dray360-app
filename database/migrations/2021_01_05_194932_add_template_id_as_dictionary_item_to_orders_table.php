<?php

use App\Models\Order;
use App\Models\DictionaryItem;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTemplateIdAsDictionaryItemToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_orders', function (Blueprint $table) {
            $table->unsignedBigInteger('tms_template_dictid')->nullable();

            $table->foreign('tms_template_dictid')->references('id')->on('t_dictionary_items');
        });

        $templates = DictionaryItem::templates()->get();
        Order::whereNotNull('tms_template_id')
            ->get()
            ->each(function ($order) use ($templates) {
                $template = $templates
                    ->filter(function ($template) use ($order) {
                        return $template->item_key == $order->tms_template_id
                            && $template->t_company_id == $order->t_company_id;
                    })
                    ->first();

                $order->update(['tms_template_dictid' => $template->id ?? null]);
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
            $table->dropColumnIfExists('tms_template_dictid');
        });
    }
}
