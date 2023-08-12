<?php

namespace App\Jobs;

use App\Enums\ScreenStatusEnum;
use App\Models\Screen;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Should be dispatched, with a two minute delay, when a screen is offline
 */
class AutomaticRebootOnFailureJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public readonly Screen $screen)
    {
    }

    public function handle(): void
    {
        // Check if screen is still OFFLINE
        if($this->screen->status === ScreenStatusEnum::OFFLINE) {
            $this->screen->updateQuietly([
                'should_restart' => true,
            ]);

            activity()
                ->causedByAnonymous()
                ->performedOn($this->screen)
                ->event('reboot')
                ->log('Scheduled automatic reboot due to offline status');
        }

    }
}
