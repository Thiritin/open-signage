<?php

namespace App\Console;

use App\Jobs\ScreenStatusCheckJob;
use App\Jobs\SyncEurofurenceScheduleJob;
use App\Jobs\ScheduleEntryDispatcherJob;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        $schedule->job(new ScreenStatusCheckJob())->everyMinute();
        $schedule->job(new ScheduleEntryDispatcherJob())->everyMinute();

        if (config('app.default_project') === "EF29") {
            $schedule->job(new SyncEurofurenceScheduleJob())->everyMinute();
        }
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
