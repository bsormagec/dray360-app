<?php

namespace App\Services\DictionaryItemsImporters;

use App\Models\DictionaryItem;
use App\Services\Apis\Blackfly;

class PtEquipmentTypesImporter extends DictionaryItemsImporter
{
    public function run(): void
    {
        if (! $this->company->blackfly_token) {
            return;
        }

        $api = new Blackfly($this->company);

        $templatesIds = collect($api->getEquipmentTypes())
            ->map(function ($item) {
                $dictionaryItem = DictionaryItem::updateOrCreate([
                    't_company_id' => $this->company->id,
                    'item_type' => $this->itemType, // DictionaryItem::PT_EQUIPMENTTYPE_TYPE
                    'item_key' => $item['id'],
                ], [
                    'item_display_name' => $item['type'] . (empty($item['line']) ? '' : " ({$item['line']})"),
                    'item_value' => $item,
                ]);

                return $dictionaryItem->id;
            })
            ->toArray();

        $this->deleteMissingValues($templatesIds);
    }
}
