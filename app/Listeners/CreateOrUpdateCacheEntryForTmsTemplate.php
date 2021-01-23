<?php

namespace App\Listeners;

use App\Models\Order;
use Illuminate\Support\Str;
use App\Models\DictionaryItem;
use App\Events\TmsTemplateVerified;
use App\Models\DictionaryCacheEntry;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\DictionaryCacheDefinition;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreateOrUpdateCacheEntryForTmsTemplate implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(TmsTemplateVerified $event)
    {
        $order = Order::query()
            ->select([
                'id',
                't_company_id',
                'tms_template_dictid',
                'bill_to_address_raw_text',
                'variant_name'
            ])
            ->with('orderAddressEvents:id,t_address_raw_text,t_order_id')
            ->find($event->orderData['id']);
        $cacheEntryData = $this->getCacheEntryDataFromOrder($order);
        $cacheEntry = DictionaryCacheEntry::firstOrCreate($cacheEntryData);
        $cacheEntry->increment('verified_count');
    }

    protected function getCacheEntryDataFromOrder(Order $order): array
    {
        $cacheDefinitionMappedData = $this->getMappedCacheDefinitionToEntryData($order);

        return $cacheDefinitionMappedData + [
            't_dictionary_item_id' => $order->tms_template_dictid,
            't_company_id' => $order->t_company_id,
            'cache_type' => DictionaryItem::TEMPLATE_TYPE,
        ];
    }

    protected function getMappedCacheDefinitionToEntryData(Order $order): array
    {
        $cacheDefinition = DictionaryCacheDefinition::template();
        return collect($cacheDefinition->toArray())
            ->filter(function ($value, $key) {
                return Str::startsWith($key, 'use_');
            })
            ->mapWithKeys(function ($value, $key) use ($order) {
                $definitionToEntryKeyMap = [
                    'use_variant_name' => 'cached_variant_name',
                    'use_bill_to_address_raw_text' => 'cached_bill_to_address_raw_text',
                    'use_event1_address_raw_text' => 'cached_event1_address_raw_text',
                    'use_event2_address_raw_text' => 'cached_event2_address_raw_text',
                ];

                if (! $value) {
                    return [$definitionToEntryKeyMap[$key] => null];
                }

                $newValue = null;
                switch ($key) {
                    case 'use_variant_name':
                        $newValue = $order->variant_name;
                        break;
                    case 'use_bill_to_address_raw_text':
                        $newValue = $order->bill_to_address_raw_text;
                        break;
                    case 'use_event1_address_raw_text':
                        $newValue = $order->orderAddressEvents->get(0)->t_address_raw_text ?? null;
                        break;
                    case 'use_event2_address_raw_text':
                        $newValue = $order->orderAddressEvents->get(1)->t_address_raw_text ?? null;
                        break;
                    default:
                        $newValue = null;
                        break;
                }

                return [$definitionToEntryKeyMap[$key] => $newValue];
            })
            ->toArray();
    }
}
