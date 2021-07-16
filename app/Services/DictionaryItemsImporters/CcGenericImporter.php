<?php

namespace App\Services\DictionaryItemsImporters;

use App\Models\DictionaryItem;
use App\Services\Apis\Compcare;

abstract class CcGenericImporter extends DictionaryItemsImporter
{
    protected string $apiMethod;
    protected string $itemKey;
    protected string $itemDisplayName;

    public function run(): void
    {
        if (! $this->company->compcare_api_key) {
            return;
        }

        $api = new Compcare($this->company);

        $templatesIds = collect($api->getToken()->{$this->apiMethod}()['data'] ?? [])
            ->map(function ($item) {
                $dictionaryItem = DictionaryItem::updateOrCreate([
                    't_company_id' => $this->company->id,
                    'item_type' => $this->itemType,
                    'item_key' => $item[$this->itemKey],
                ], [
                    'item_display_name' => $item[$this->itemDisplayName],
                    'item_value' => $item,
                ]);

                return $dictionaryItem->id;
            })
            ->toArray();

        $this->deleteMissingValues($templatesIds);
    }
}
