<?php

namespace App\Console\Commands;

use App\Jobs\SyncEurofurenceScheduleJob;
use Illuminate\Console\Command;

class SyncEfscheduleCommand extends Command
{
    protected $signature = 'sync:efschedule';

    protected $description = 'Eurofurence Schedule Sync';

    public function handle(): void
    {
        SyncEurofurenceScheduleJob::dispatchSync();
    }
}
