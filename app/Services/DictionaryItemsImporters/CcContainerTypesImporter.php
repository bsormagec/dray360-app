<?php

namespace App\Services\DictionaryItemsImporters;

class CcContainerTypesImporter extends CcGenericImporter
{
    public function run(): void
    {
        $this->itemKey = 'ContainerTypeCode';
        $this->itemDisplayName = 'ContainerTypeDescription';
        $this->apiMethod = 'getContainerTypes';

        parent::run();
    }
}
