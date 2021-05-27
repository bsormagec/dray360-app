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
        $schedule
            ->command('import:profit-tools-addresses')
            ->weeklyOn(7)
            ->onOneServer();
        $schedule
            ->command('import:cargowise-addresses')
            ->dailyAt('03:00')
            ->onOneServer();
        $schedule
            ->command('import:profit-tools-addresses', ['--insert-only'])
            ->hourly()
            ->onOneServer();

        $schedule
            ->command('metrics:companies-daily')
            ->dailyAt('06:00');
        $schedule
            ->command('metrics:companies-daily', [
                '--from' => now()->toDateString(),
                '--to' => now()->toDateString(),
            ])
            ->hourly();

        $schedule->command('horizon:snapshot')->everyFiveMinutes();
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
