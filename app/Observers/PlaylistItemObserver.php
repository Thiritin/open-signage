<?php

namespace App\Observers;

use App\Events\UpdateScreenPlaylistEvent;
use App\Models\Playlist;
use App\Models\PlaylistItem;
use App\Models\Screen;

class PlaylistItemObserver
{
    public function created(PlaylistItem $playlistItem): void
    {

    }

    public function updated(PlaylistItem $playlistItem): void
    {
        // Get Playlist from PlaylistItem and then Screen from Playlist and run broadcast on each
        $playlistItem->playlist->screens->each(fn(Screen $screen) => broadcast(new UpdateScreenPlaylistEvent($screen)));
    }

    public function deleted(PlaylistItem $playlistItem): void
    {
    }

    public function restored(PlaylistItem $playlistItem): void
    {
    }

    public function forceDeleted(PlaylistItem $playlistItem): void
    {
    }
}
