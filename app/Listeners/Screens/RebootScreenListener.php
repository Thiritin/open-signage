<?php

namespace App\Listeners\Screens;

use App\Events\Broadcast\RefreshScreenEvent;
use App\Events\Screens\OfflineEvent;
use App\Jobs\AutomaticRebootOnFailureJob;
use Illuminate\Contracts\Queue\ShouldQueue;

class RebootScreenListener implements ShouldQueue
{
    public function __construct()
    {
    }

    public function handle(OfflineEvent $event): void
    {
        // Ask window refresh
        broadcast(new RefreshScreenEvent($event->screen));
        activity()
            ->causedByAnonymous()
            ->performedOn($event->screen)
            ->event('refresh')
            ->log('Refreshing webpage due to offline status');

        // Schedule Automated Reboot in 2 minutes, should the refresh not solved the issue
        AutomaticRebootOnFailureJob::dispatch($event->screen)->delay(now()->addMinutes(2));
    }
}
