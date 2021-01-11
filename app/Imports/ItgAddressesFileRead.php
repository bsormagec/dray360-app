<?php

namespace App\Imports;

use App\Jobs\ImportItgCargoWiseAddresses;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ItgAddressesFileRead implements WithMultipleSheets
{
    use Importable;

    protected ImportItgCargoWiseAddresses $job;

    public function __construct(ImportItgCargoWiseAddresses $job)
    {
        $this->job = $job;
    }

    public function sheets(): array
    {
        return [
            0 => new ItgBillToAddressesSheet($this->job),
            1 => new ItgAllAddressesSheet($this->job),
        ];
    }
}
