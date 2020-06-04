<?php

namespace App\Jobs;

use App\Models\Address;
use App\Models\Company;
use App\Models\TMSProvider;
use App\Services\Apis\RipCms;
use App\Models\CompanyAddressTMSCode;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Spatie\RateLimitedMiddleware\RateLimited;

class ImportProfitToolsAddress implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;

    public $queue = 'imports';
    public $tries = 5;
    public $timeout = 15;
    public $maxExceptions = 3;

    public int $companyId;
    public int $tmsProviderId;
    public $addressCode;

    public function __construct($companyAddress, Company $company, TMSProvider $tmsProvider)
    {
        $this->addressCode = $companyAddress['id'];
        $this->companyId = $company->id;
        $this->tmsProviderId = $tmsProvider->id;
    }

    public function handle()
    {
        $address = (new RipCms())->getCompany($this->addressCode);

        $companyAddressTmsProvider = CompanyAddressTMSCode::query()
            ->forCompanyTmsProvider($this->companyId, $this->tmsProviderId)
            ->where('company_address_tms_code', $this->addressCode)
            ->with('address')
            ->first();

        if ($companyAddressTmsProvider) {
            $this->updateAddress($companyAddressTmsProvider->address, $address);
            return;
        }

        CompanyAddressTMSCode::createFrom(
            $this->addressCode,
            Address::createFrom($address, 'ripcms'),
            $this->companyId,
            $this->tmsProviderId
        );
    }

    protected function updateAddress(Address $oldAddress, $newData)
    {
        if ($oldAddress->isTheSameAs($newData, 'ripcms')) {
            return;
        }

        $oldAddress->updateFrom($newData, 'ripcms');
    }

    /**
     * Get the middleware the job should pass through.
     */
    public function middleware(): array
    {
        $rateLimited = (new RateLimited())
            ->allow(300)
            ->everySeconds(60)
            ->releaseAfterBackoff($this->attempts());

        return [$rateLimited];
    }

    /**
     * Determine the time at which the job shouldn't be tried more.
     */
    public function retryUntil(): \DateTime
    {
        return now()->addMinutes(2);
    }

    public function tags(): array
    {
        return ['import:profit-tools-addresses'];
    }
}
