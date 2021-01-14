<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use App\Jobs\ImportItgCargoWiseAddresses;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ItgAllAddressesSheet implements ToCollection, WithHeadingRow
{
    protected ImportItgCargoWiseAddresses $job;

    public function __construct(ImportItgCargoWiseAddresses $job)
    {
        $this->job = $job;
    }

    public function collection(Collection $collection)
    {
        $this->job->setAddresses($collection);
    }
}
