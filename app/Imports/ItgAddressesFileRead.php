<?php

namespace App\Imports;

use Illuminate\Support\Str;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Facades\Storage;
use App\Jobs\ImportItgCargoWiseAddresses;

class ItgAddressesFileRead
{
    protected ImportItgCargoWiseAddresses $job;

    public function __construct(ImportItgCargoWiseAddresses $job)
    {
        $this->job = $job;
    }

    public function import($path, $disk)
    {
        $file = Storage::disk($disk)->get($path);
        $extension = Str::of($path)->afterLast('.')->lower()->__toString();
        $fileName = sha1($file).".{$extension}";
        Storage::disk('local')->put($fileName, $file);
        $headers = [
            'org_code',
            'org_name',
            'address_line_1',
            'address_line_2',
            'city',
            'state',
            'post_code',
            'receivable',
            'payable',
            'consignor',
            'consignee',
            'transport_client',
            'warehouse_client',
            'carrier',
            'forwarder',
            'broker',
            'services',
            'competitor',
            'sales',
        ];

        $allAddresses = (new FastExcel())
            ->withoutHeaders()
            ->import(storage_path('app/'.$fileName))
            ->reject(fn ($items) => implode('', $items) === '')
            ->map(function ($row) use ($headers) {
                return collect($row)->mapWithKeys(fn ($item, $index) => [$headers[$index] => $item]);
            });

        Storage::disk('local')->delete($fileName);

        $this->job->setAddresses($allAddresses);
    }
}
