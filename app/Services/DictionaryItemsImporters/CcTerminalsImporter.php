<?php

namespace App\Services\DictionaryItemsImporters;

class CcTerminalsImporter extends CcGenericImporter
{
    public function run(): void
    {
        $this->itemKey = 'TerminalCode';
        $this->itemDisplayName = 'TerminalDescription';
        $this->apiMethod = 'getTerminals';

        parent::run();
    }
}
