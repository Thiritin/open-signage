<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Playlist;
use App\Models\PlaylistItem;
use App\Models\ScheduleEntry;
use App\Models\Screen;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ScreenController extends Controller
{
    public function __invoke(Request $request, $slug = null)
    {
        $finalSlug = $slug ?? $request->get('kiosk') ?? null;
        abort_if(is_null($slug), 400, "No slug provided");

        $screen = Screen::firstOrCreate(['slug' => $finalSlug],[
            'name' => 'New Screen '.$finalSlug,
            'slug' => $finalSlug,
            'playlist_id' => Playlist::first()->id,
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
