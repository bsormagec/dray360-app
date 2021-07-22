<?php

namespace App\Services\DictionaryItemsImporters;

class CcContainerSizesImporter extends CcGenericImporter
{
    public function run(): void
    {
        $this->itemKey = 'ContainerSizeCode';
        $this->itemDisplayName = 'ContainerSizeDescription';
        $this->apiMethod = 'getContainerSizes';

        parent::run();
    }
}
