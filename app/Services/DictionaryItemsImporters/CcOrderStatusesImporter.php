<?php

namespace App\Services\DictionaryItemsImporters;

class CcOrderStatusesImporter extends CcGenericImporter
{
    public function run(): void
    {
        $this->itemKey = 'OrderStatusCode';
        $this->itemDisplayName = 'OrderStatusDescription';
        $this->apiMethod = 'getOrderStatuses';

        parent::run();
    }
}
