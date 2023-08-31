<?php

namespace App\Observers;

use App\Models\RoomScreen;

class RoomScreenObserver
{
    public function created(RoomScreen $roomScreen): void
    {
        // Update Screen
        $roomScreen->screen->sendScreensUpdate();
    }

    public function updated(RoomScreen $roomScreen): void
    {
        // Update Screen
        $roomScreen->screen->sendScreensUpdate();
    }

    public function deleted(RoomScreen $roomScreen): void
    {
        // Update Screen
        $roomScreen->screen->sendScreensUpdate();
    }

    public function restored(RoomScreen $roomScreen): void
    {
    }

    public function forceDeleted(RoomScreen $roomScreen): void
    {
    }
}
