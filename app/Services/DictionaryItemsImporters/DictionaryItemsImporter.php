<?php

namespace App\Services\DictionaryItemsImporters;

use App\Models\Company;
use App\Models\DictionaryItem;

abstract class DictionaryItemsImporter
{
    protected Company $company;
    protected array $settings;
    protected string $itemType;

    public function __construct(Company $company, array $settings)
    {
        $this->company = $company;
        $this->settings = $settings;
        $this->itemType = $settings['item_type'];
    }

    abstract public function run(): void;

    /**
     * Delete from the DB the items that are not included in the ids list.
     */
    protected function deleteMissingValues(array $ids): void
    {
        if (! isset($this->settings['delete_missing']) || ! $this->settings['delete_missing']) {
            return;
        }

        DictionaryItem::whereNotIn('id', $ids)->delete();
    }
}
