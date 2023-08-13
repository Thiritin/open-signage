<?php

namespace App\Jobs;

use App\Models\ScheduleEntry;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ScheduleEntryDispatcherJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
    }

    public function handle(): void
    {
        ScheduleEntry::whereNotNull('automation')
            ->whereDate('starts_at', now()->toDateString())
            ->get()->each(fn(ScheduleEntry $entry) => ScheduleEntryAutomationJob::dispatch($entry));
    }
}
