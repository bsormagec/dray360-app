<?php

namespace Tests\MockClasses;

use App\Models\DictionaryItem;
use App\Services\DictionaryItemsImporters\DictionaryItemsImporter;

class MockDictionaryItemImporter extends DictionaryItemsImporter
{
    const KEY = 'something123',
        DISPLAY_NAME = 'Something 1 2 3';

    public function run(): void
    {
        DictionaryItem::create([
            't_company_id' => $this->company->id,
            'item_type' => $this->itemType,
            'item_key' => self::KEY,
            'item_display_name' => self::DISPLAY_NAME,
        ]);
    }
}
