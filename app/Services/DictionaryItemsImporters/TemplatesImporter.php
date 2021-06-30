<?php

namespace App\Services\DictionaryItemsImporters;

use App\Models\DictionaryItem;
use App\Services\Apis\Blackfly;

class TemplatesImporter extends DictionaryItemsImporter
{
    public function run(): void
    {
        if (! $this->company->blackfly_token) {
            return;
        }

        $api = new Blackfly($this->company);

        $templatesIds = collect($api->getTemplates())
            ->map(function ($item) {
                $dictionaryItem = DictionaryItem::updateOrCreate([
                    't_company_id' => $this->company->id,
                    'item_type' => $this->itemType, // DictionaryItem::TEMPLATE_TYPE
                    'item_key' => $item['ds_id'],
                ], [
                    'item_display_name' => $item['ds_ref1_text'],
                    'item_value' => $item,
                ]);

                return $dictionaryItem->id;
            })
            ->toArray();

        $this->deleteMissingValues($templatesIds);
    }
}
