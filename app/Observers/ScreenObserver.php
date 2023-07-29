<?php

namespace App\Observers;

use App\Events\UpdateScreenPlaylistEvent;
use App\Models\Screen;

class ScreenObserver
{
    public function created(Screen $screen): void
    {
        if ($screen->playlist_id !== null) {
            broadcast(new UpdateScreenPlaylistEvent($screen));
        }
    }

    public function updated(Screen $screen): void
    {
        if ($screen->playlist_id !== null) {
            broadcast(new UpdateScreenPlaylistEvent($screen));
        }
    }

    public function deleted(Screen $screen): void
    {
    }

    public function restored(Screen $screen): void
    {
    }

    public function forceDeleted(Screen $screen): void
    {
    }
}
