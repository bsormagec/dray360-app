<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [

    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Profit Tools Imports
        $schedule
            ->command('import:profit-tools-addresses')
            ->weeklyOn(7)
            ->onOneServer();
        $schedule
            ->command('import:profit-tools-addresses', ['--insert-only'])
            ->hourly()
            ->onOneServer();

        // Cargo Wise Imports
        $schedule
            ->command('import:cargowise-addresses')
            ->dailyAt('03:00')
            ->onOneServer();

        // Compcare Imports
        $schedule
            ->command('import:compcare-addresses')
            ->weeklyOn(6, '03:00')
            ->onOneServer();

        // Metrics
        $schedule
            ->command('metrics:companies-daily')
            ->dailyAt('06:00')
            ->onOneServer();
        $schedule
            ->command('metrics:companies-daily', [
                '--from' => now()->toDateString(),
                '--to' => now()->toDateString(),
            ])
            ->hourly()
            ->onOneServer();

        // Dictionary Items Sync
        $schedule->command('import:dictionary-items')
            ->hourly()
            ->onOneServer();

        $schedule->command('horizon:snapshot')
            ->everyFiveMinutes()
            ->onOneServer();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
