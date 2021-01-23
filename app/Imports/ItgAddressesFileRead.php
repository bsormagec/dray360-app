<?php

namespace App\Imports;

use Illuminate\Support\Str;
use Illuminate\Support\Collection;
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
        $extension = Str::afterLast($path, '.');
        $fileName = sha1($file).".{$extension}";
        Storage::disk('local')->put($fileName, $file);

        $billToSheet = (new FastExcel())
            ->sheet(1)
            ->import(storage_path('app/'.$fileName));
        $allAddressesSheet = (new FastExcel())
            ->sheet(2)
            ->import(storage_path('app/'.$fileName));


        Storage::disk('local')->delete($fileName);


        $this->job->setBillToAddresses(
            $this->mapHeadersToSnakeCase($billToSheet)->pluck('code')
        );
        $this->job->setAddresses($this->mapHeadersToSnakeCase($allAddressesSheet));
    }

    protected function mapHeadersToSnakeCase(Collection $rows)
    {
        return $rows->map(function ($row) {
            return collect($row)
                ->mapWithKeys(fn ($item, $key) => [Str::snake($key) => $item])
                ->toArray();
        });
    }
}
