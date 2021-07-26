<?php

namespace App\Services\DictionaryItemsImporters;

class CcCarriersImporter extends CcGenericImporter
{
    public function run(): void
    {
        $this->itemKey = 'CarrierScac';
        $this->itemDisplayName = 'CarrierName';
        $this->apiMethod = 'getCarriers';

        parent::run();
    }
}
