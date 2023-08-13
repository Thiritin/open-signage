<?php

namespace App\Jobs;

use App\Models\ScheduleEntry;
use App\Models\Screen;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ScheduleEntryAutomationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public ScheduleEntry $entry)
    {
    }

    public function handle(): void
    {
        $entry = $this->entry;
        $newAutomation = collect($entry->automation)->map(function (array $automation) use ($entry) {
            if (isset($automation['has_run']) && $automation['has_run']) {
                return $automation;
            }

            // Different Type Checks
            $continue = match ($automation['type']) {
                'on_start' => $entry->starts_at->isPast(),
                'on_end' => $entry->ends_at->isPast(),
                'on_start_with_delay' => $entry->starts_at->addMinutes($entry->delay)->isPast(),
                'on_end_with_delay' => $entry->ends_at->addMinutes($entry->delay)->isPast(),
            };

            if (!$continue) {
                return $automation;
            }

            Screen::whereHas('playlist', fn($q) => ($q->normal()))
                ->whereIn('id', $automation['screens'])
                ->update(['playlist_id' => $automation['playlist']]);

            $automation['has_run'] = true;
            return $automation;
        })->toArray();
        $entry->update(['automation' => $newAutomation]);
    }
}
