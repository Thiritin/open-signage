<?php

namespace App\Listeners\Screens;

use App\Enums\ScreenStatusEnum;
use App\Events\Screens\OfflineEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ScreenStatusOffline implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(OfflineEvent $event): void
    {
        $event->screen->updateQuietly([
            'status' => ScreenStatusEnum::OFFLINE,
        ]);

        activity()
            ->causedByAnonymous()
            ->performedOn($event->screen)
            ->event('offline')
            ->log('Screen is offline');
    }
}
