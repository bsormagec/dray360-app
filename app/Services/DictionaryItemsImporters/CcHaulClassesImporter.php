<?php

namespace App\Services\DictionaryItemsImporters;

class CcHaulClassesImporter extends CcGenericImporter
{
    public function run(): void
    {
        $this->itemKey = 'HaulClassCode';
        $this->itemDisplayName = 'HaulClassDescription';
        $this->apiMethod = 'getHaulClasses';

        parent::run();
    }
}
