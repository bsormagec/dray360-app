<?php

namespace App\Services\DictionaryItemsImporters;

class CcOrderClassesImporter extends CcGenericImporter
{
    public function run(): void
    {
        $this->itemKey = 'OrderClassCode';
        $this->itemDisplayName = 'OrderClassDescription';
        $this->apiMethod = 'getOrderClasses';

        parent::run();
    }
}
