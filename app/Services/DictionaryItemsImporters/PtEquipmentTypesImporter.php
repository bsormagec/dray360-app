<?php

namespace App\Services\DictionaryItemsImporters;

use Illuminate\Support\Str;
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
        $configuration = app('tenancy')->getCompanyConfiguration($this->company);

        $templatesIds = collect($api->getEquipmentTypes())
            ->map(function ($item) use ($configuration) {
                $displayName = $item['type'] . (empty($item['line']) ? '' : " ({$item['line']})");
                $containerCode = Str::contains($displayName, $configuration['pt_equipment_chassis_keywords'] ?? [])
                    ? 'CH'
                    : 'CO';
                $item['lineprefix'] = Str::of($item['lineprefix'])
                    ->remove("\t")
                    ->trim()
                    ->explode(',')
                    ->map(fn ($string) => trim($string))
                    ->filter();

                $dictionaryItem = DictionaryItem::updateOrCreate([
                    't_company_id' => $this->company->id,
                    'item_type' => $this->itemType, // DictionaryItem::PT_EQUIPMENTTYPE_TYPE
                    'item_key' => $item['id'],
                ], [
                    'item_display_name' => $displayName,
                    'item_value' => $item + ['chassis_container_code' => $containerCode],
                ]);

                return $dictionaryItem->id;
            })
            ->toArray();

        $this->deleteMissingValues($templatesIds);
    }
}
