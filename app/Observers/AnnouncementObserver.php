<?php

namespace App\Observers;

use App\Events\UpdateAnnouncementEvent;
use App\Models\Announcement;

class AnnouncementObserver
{
    public function created(Announcement $announcement): void
    {
        broadcast(new UpdateAnnouncementEvent);
    }

    public function updated(Announcement $announcement): void
    {
        broadcast(new UpdateAnnouncementEvent);
    }

    public function deleted(Announcement $announcement): void
    {
        broadcast(new UpdateAnnouncementEvent);
    }

    public function restored(Announcement $announcement): void
    {
    }

    public function forceDeleted(Announcement $announcement): void
    {
    }
}
