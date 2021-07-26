<?php

namespace App\Listeners;

use App\Models\Order;
use Illuminate\Support\Str;
use App\Models\DictionaryItem;
use App\Events\AttributeVerified;
use App\Models\DictionaryCacheEntry;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\DictionaryCacheDefinition;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreateOrUpdateCacheEntry implements ShouldQueue
{
    use InteractsWithQueue;

    const VERIFIED_TO_ITEM_TYPE_MAP = [
        'tms_template_dictid_verified' => DictionaryItem::TEMPLATE_TYPE,
        'itgcontainer_dictid_verified' => DictionaryItem::ITGCONTAINER_TYPE,
        'carrier_dictid_verified' => DictionaryItem::CARRIER_TYPE,
        'vessel_dictid_verified' => DictionaryItem::VESSEL_TYPE,
        'cc_loadtype_dictid_verified' => DictionaryItem::CC_LOADTYPE_TYPE,
        'cc_orderstatus_dictid_verified' => DictionaryItem::CC_ORDERSTATUS_TYPE,
        'cc_haulclass_dictid_verified' => DictionaryItem::CC_HAULCLASS_TYPE,
        'cc_orderclass_dictid_verified' => DictionaryItem::CC_ORDERCLASS_TYPE,
        'cc_loadedempty_dictid_verified' => DictionaryItem::CC_LOADEDEMPTY_TYPE,
        'termdiv_dictid_verified' => DictionaryItem::TERMDIV_TYPE,
        'cc_containersize_dictid_verified' => DictionaryItem::CC_CONTAINERSIZE_TYPE,
        'cc_containertype_dictid_verified' => DictionaryItem::CC_CONTAINERTYPE_TYPE,
    ];

    protected $verifiableColumn;

    /**
     * Handle the event.
     */
    public function handle(AttributeVerified $event)
    {
        $this->verifiableColumn = $event->verifiableColumn;
        $order = Order::query()
            ->select([
                'id',
                't_company_id',
                'bill_to_address_raw_text',
                'variant_name',
                'hazardous',
                'equipment_size',
                'vessel',
                'carrier',
                'shipment_direction',
                'tms_template_dictid',
                'itgcontainer_dictid',
                'carrier_dictid',
                'vessel_dictid',
            ])
            ->with([
                'orderAddressEvents:id,t_address_raw_text,t_order_id',
                'tmsTemplate:id,item_key',
            ])
            ->find($event->orderData['id']);
        $cacheEntryData = $this->getCacheEntryDataFromOrder($order);

        if (! $cacheEntryData) {
            return;
        }

        $cacheEntry = DictionaryCacheEntry::firstOrCreate($cacheEntryData);
        $cacheEntry->increment('verified_count');
    }

    protected function getCacheEntryDataFromOrder(Order $order): ?array
    {
        $cacheDefinitionMappedData = $this->getMappedCacheDefinitionToEntryData($order);
        $orderDictionaryItemIdColumn = Str::before($this->verifiableColumn, '_verified');
        $itemType = self::VERIFIED_TO_ITEM_TYPE_MAP[$this->verifiableColumn];
        $dictionaryItemId = $order->getAttribute($orderDictionaryItemIdColumn);

        if (! $dictionaryItemId) {
            return null;
        }

        $isValidType = DictionaryItem::query()
            ->where([
                'id' => $dictionaryItemId,
                'item_type' => $itemType,
            ])
            ->exists();
        if (! $isValidType) {
            return null;
        }

        return $cacheDefinitionMappedData + [
            't_dictionary_item_id' => $dictionaryItemId,
            't_company_id' => $order->t_company_id,
            'cache_type' => $itemType,
        ];
    }

    protected function getMappedCacheDefinitionToEntryData(Order $order): array
    {
        $itemType = self::VERIFIED_TO_ITEM_TYPE_MAP[$this->verifiableColumn];
        $cacheDefinition = DictionaryCacheDefinition::cacheDefinitionForType($itemType);

        return collect($cacheDefinition->toArray())
            ->filter(function ($value, $key) {
                return Str::startsWith($key, 'use_');
            })
            ->mapWithKeys(function ($value, $key) use ($order) {
                $cacheEntryColumn = Str::replaceFirst('use_', 'cached_', $key);

                if (! $value) {
                    return [$cacheEntryColumn => null];
                }

                $newValue = null;
                $matches = null;
                if (preg_match('/use_event([0-9])_address_raw_text/', $key, $matches)) {
                    $index = intval($matches[1]) - 1;
                    $newValue = $order->orderAddressEvents->get($index)->t_address_raw_text ?? null;
                } elseif ($key == 'use_template_key') {
                    $newValue = $order->tmsTemplate->item_key ?? null;
                } else {
                    $column = Str::after($key, 'use_');
                    $newValue = $column ? $order->getAttribute($column) : null;
                }

                return [$cacheEntryColumn => $newValue];
            })
            ->toArray();
    }
}
