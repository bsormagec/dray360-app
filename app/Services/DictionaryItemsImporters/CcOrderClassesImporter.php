<?php

namespace App\Services\DictionaryItemsImporters;

class CcOrderClassesImporter extends CcGenericImporter
{
    public function run(): void
    {
        $this->itemKey = 'OrderClassId';
        $this->itemDisplayName = 'OrderClassCode';
        $this->apiMethod = 'getOrderClasses';

        parent::run();
    }
}
