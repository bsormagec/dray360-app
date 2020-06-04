<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Models\TMSProvider;
use App\Services\Apis\RipCms;
use Illuminate\Console\Command;
use App\Jobs\ImportProfitToolsAddress;

class ImportProfitToolsAddresses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:profit-tools-addresses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports all the addresses from ripcms for Profit Tools and Cushing';

    protected RipCms $ripCmsApi;

    public function __construct(RipCms $ripCmsApi)
    {
        parent::__construct();

        $this->ripCmsApi = $ripCmsApi;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Getting all the information from RipCms');
        $companiesAddress = collect($this->ripCmsApi->getCompanies());
        $company = Company::getCushing();
        $tmsProvider = TMSProvider::getProfitTools();

        $this->info('Queueing the individual imports');
        $companiesAddress->each(function ($address) use ($company, $tmsProvider) {
            ImportProfitToolsAddress::dispatch($address, $company, $tmsProvider);
        });
    }
}
