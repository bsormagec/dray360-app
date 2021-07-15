<?php

namespace App\Services\DictionaryItemsImporters;

use App\Models\DictionaryItem;
use App\Services\Apis\Compcare;

class CcHaulClassesImporter extends DictionaryItemsImporter
{
    public function run(): void
    {
        if (! $this->company->compcare_api_key) {
            return;
        }

        $api = new Compcare($this->company);

        $templatesIds = collect($api->getToken()->getHaulClasses()['data'] ?? [])
            ->map(function ($item) {
                $dictionaryItem = DictionaryItem::updateOrCreate([
                    't_company_id' => $this->company->id,
                    'item_type' => $this->itemType, // DictionaryItem::CC_HAULCLASS_TYPE
                    'item_key' => $item['HaulClassId'],
                ], [
                    'item_display_name' => $item['HaulClassCode'],
                    'item_value' => $item,
                ]);

                return $dictionaryItem->id;
            })
            ->toArray();

        $this->deleteMissingValues($templatesIds);
    }
}
