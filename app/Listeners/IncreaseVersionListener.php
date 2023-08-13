<?php

namespace App\Listeners;

use App\Events\UpdateAnnouncementEvent;
use App\Events\UpdateScheduleEvent;
use App\Events\UpdateScreenPlaylistEvent;
use App\Models\Screen;
use Illuminate\Support\Facades\DB;

class IncreaseVersionListener
{
    public function __construct()
    {
    }

    public function handle(UpdateAnnouncementEvent|UpdateScheduleEvent|UpdateScreenPlaylistEvent $event): void
    {
        // If Event is UpdateScreenPlaylistEvent increase only one screen version
        if ($event instanceof UpdateScreenPlaylistEvent) {
            $event->screen->version++;
            $event->screen->saveQuietly();
            return;
        }
        // Any other event will increase all screens version
        DB::table('screens')->increment('version');
    }
}
