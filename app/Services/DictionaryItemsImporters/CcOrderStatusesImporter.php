<?php

namespace App\Services\DictionaryItemsImporters;

class CcOrderStatusesImporter extends CcGenericImporter
{
    public function run(): void
    {
        $this->itemKey = 'OrderStatusId';
        $this->itemDisplayName = 'OrderStatusCode';
        $this->apiMethod = 'getOrderStatuses';

        parent::run();
    }
}
