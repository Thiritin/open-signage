<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Playlist;
use App\Models\PlaylistItem;
use App\Models\ScheduleEntry;
use App\Models\Screen;
use Inertia\Inertia;

class ScreenController extends Controller
{
    public function __invoke($slug)
    {
        $screen = Screen::firstOrCreate(['slug' => $slug],[
            'name' => 'New Screen '.$slug,
            'slug' => $slug,
            'playlist_id' => null,
            'provisioned' => false,
        ]);

        return Inertia::render('Main', [
            'initialPages' => $screen->playlist?->playlistItems->map(fn(PlaylistItem $playlistItem) => [
                'layout' => $playlistItem->layout->component,
                'component' => $playlistItem->page->component,
                'props' => $playlistItem->content,
                'duration' => $playlistItem->duration,
                'title' => $playlistItem->title ?? '',
            ]) ?? [],
            'screen' => $screen,
            'initialAnnouncements' => Announcement::all()->toArray(),
            'initialSchedule' => ScheduleEntry::all()->toArray(),
        ]);
    }
}
