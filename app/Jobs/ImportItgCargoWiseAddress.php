<?php

namespace App\Jobs;

use App\Models\Company;
use Illuminate\Support\Str;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Spatie\RateLimitedMiddleware\RateLimited;

class ImportItgCargoWiseAddress extends ImportAddressBase implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;

    public $queue = 'imports';
    public $tries = 5;
    public $timeout = 15;
    public $maxExceptions = 3;

    public function __construct($address, $tmsProviderId, Company $company)
    {
        $this->addressCode = $address['org_code'];
        $this->address = $address;
        $this->companyId = $company->id;
        $this->companyName = $company->name;
        $this->tmsProviderId = $tmsProviderId;
    }

    protected function getAddressData()
    {
        return $this->address;
    }

    protected function getTmsProviderCode(): string
    {
        return 'itg-cargowise';
    }

    /**
     * Get the middleware the job should pass through.
     */
    public function middleware(): array
    {
        $rateLimited = (new RateLimited())
            ->allow(10000)
            ->everySeconds(60)
            ->releaseAfterOneMinute();

        return [$rateLimited];
    }

    /**
     * Determine the time at which the job shouldn't be tried more.
     */
    public function retryUntil(): \DateTime
    {
        return now()->addHours(3);
    }

    public function tags(): array
    {
        return [
            'import:itg-cargowise',
            'import-address-'.Str::snake($this->companyName),
        ];
    }
}
