<?php

namespace App\Services\DictionaryItemsImporters;

class CcLoadTypesImporter extends CcGenericImporter
{
    public function run(): void
    {
        $this->itemKey = 'LoadTypeId';
        $this->itemDisplayName = 'LoadTypeCode';
        $this->apiMethod = 'getLoadTypes';

        parent::run();
    }
}
