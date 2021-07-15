<?php

namespace App\Services\DictionaryItemsImporters;

class CcLoadTypesImporter extends CcGenericImporter
{
    public function run(): void
    {
        $this->itemKey = 'LoadTypeCode';
        $this->itemDisplayName = 'LoadTypeDescription';
        $this->apiMethod = 'getLoadTypes';

        parent::run();
    }
}
