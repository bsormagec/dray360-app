<?php

namespace App\Console\Commands\Metrics;

use App\Models\Company;
use Illuminate\Support\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use App\Jobs\Metrics\ComputeCompanyDailyMetrics;

class ComputeCompaniesDailyMetrics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'metrics:companies-daily {--company-id=} {--from= : yyyy-mm-dd date} {--to= : yyyy-mm-dd date} {--all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Compute daily metrics for all companies';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if (! $this->datesAreValid()) {
            $this->error('The date range provided is invalid');
            return 1;
        }

        $startDate = $this->getDate('from');
        $endDate = $this->getDate('to');
        $companies = Company::query()
            ->when(
                $this->option('all'),
                fn ($q) => $q->withTrashed(),
                fn ($q) => $q->active()
            )
            ->when($this->option('company-id'), function (Builder $query) {
                $query->where('id', $this->option('company-id'));
            })
            ->get(['id', 'name']);

        $this->alert('Queueing individual computing jobs');
        collect($startDate->daysUntil($endDate))
            ->crossJoin($companies)
            ->each(function ($params) {
                [$date, $company] = $params;
                $this->info('Company Id: ' . $company->id . ' / Date: '. $date->toDateTimeString());
                ComputeCompanyDailyMetrics::dispatch($date, $company);
            });
    }

    protected function datesAreValid()
    {
        $startDate = $this->option('from');
        $endDate = $this->option('to');


        if (! $startDate && ! $endDate) {
            return true;
        }

        if (! $startDate || ! $endDate) {
            return false;
        }

        $startDate = Carbon::createFromDate($startDate);
        $endDate = Carbon::createFromDate($endDate);

        return $startDate->isBefore($endDate) || $startDate->isSameDay($endDate);
    }

    protected function getDate($key): Carbon
    {
        return $this->option($key)
            ? Carbon::createFromDate($this->option($key))
            : now()->yesterday();
    }
}
