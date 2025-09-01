<?php

namespace App\Observers;

use App\Events\UpdateScreenPlaylistEvent;
use App\Jobs\ConvertAnyFileJob;
use App\Models\Playlist;
use App\Models\PlaylistItem;
use App\Models\Screen;
use Illuminate\Support\Facades\Bus;

class PlaylistItemObserver
{
    public function created(PlaylistItem $playlistItem): void
    {
        Bus::chain([
            fn () => ConvertAnyFileJob::dispatch(),
            fn () => $playlistItem->playlist->screens->each(fn (Screen $screen) => broadcast(new UpdateScreenPlaylistEvent($screen))),
        ])->dispatch();
    }

    public function updated(PlaylistItem $playlistItem): void
    {
        // Get Playlist from PlaylistItem and then Screen from Playlist and run broadcast on each
        Bus::chain([
            fn () => ConvertAnyFileJob::dispatch(),
            fn () => $playlistItem->playlist->screens->each(fn (Screen $screen) => broadcast(new UpdateScreenPlaylistEvent($screen))),
        ])->dispatch();
    }

    public function deleted(PlaylistItem $playlistItem): void
    {
        $playlistItem->playlist->screens->each(fn (Screen $screen) => broadcast(new UpdateScreenPlaylistEvent($screen)));
    }

    public function restored(PlaylistItem $playlistItem): void
    {
    }

    public function forceDeleted(PlaylistItem $playlistItem): void
    {
    }
}
