<?php

namespace App\Jobs;

use App\Models\Company;
use App\Models\TMSProvider;
use Illuminate\Support\Str;
use App\Services\Apis\RipCms;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Spatie\RateLimitedMiddleware\RateLimited;

class ImportProfitToolsAddress extends ImportAddressBase implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;

    public $queue = 'imports';
    public $tries = 5;
    public $timeout = 15;
    public $maxExceptions = 3;

    public function __construct($companyAddress, Company $company, TMSProvider $tmsProvider)
    {
        $this->addressCode = $companyAddress['id'];
        $this->companyId = $company->id;
        $this->companyName = $company->name;
        $this->tmsProviderId = $tmsProvider->id;
    }

    protected function getAddressData()
    {
        $company = Company::find($this->companyId);
        return (new RipCms($company))
            ->getToken()
            ->getCompany($this->addressCode);
    }

    protected function getTmsProviderCode(): string
    {
        return 'ripcms';
    }

    /**
     * Get the middleware the job should pass through.
     */
    public function middleware(): array
    {
        $rateLimited = (new RateLimited())
            ->allow(1000)
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
            'import:profit-tools-addresses',
            'import-address-'.Str::snake($this->companyName),
        ];
    }
}
