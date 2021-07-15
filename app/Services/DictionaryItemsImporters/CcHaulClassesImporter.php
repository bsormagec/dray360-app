<?php

namespace App\Services\DictionaryItemsImporters;

class CcHaulClassesImporter extends CcGenericImporter
{
    public function run(): void
    {
        $this->itemKey = 'HaulClassId';
        $this->itemDisplayName = 'HaulClassCode';
        $this->apiMethod = 'getHaulClasses';

        parent::run();
    }
}
