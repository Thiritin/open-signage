<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Playlist;
use App\Models\PlaylistItem;
use App\Models\ScheduleEntry;
use App\Models\Scopes\HideEmergencyScope;
use App\Models\Screen;
use App\Settings\GeneralSettings;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ScreenController extends Controller
{
    public function __invoke(Request $request, $slug = null)
    {
        $finalSlug = $slug ?? $request->get('kiosk') ?? null;
        abort_if(is_null($finalSlug), 400, "No slug provided");

        $screen = Screen::firstOrCreate(['slug' => $finalSlug], [
            'name' => 'New Screen '.$finalSlug,
            'slug' => $finalSlug,
            'playlist_id' => app(GeneralSettings::class)->playlist_id,
            'provisioned' => false,
        ]);

        return Inertia::render('Main', [
            'initialPages' => $screen->playlist->playlistItems->map(fn(PlaylistItem $playlistItem) => [
                'layout' => [
                    'component' => $playlistItem->layout->component,
                    'path' => $playlistItem->layout->project->path,
                ],
                'path' => $playlistItem->page->project->path,
                'component' => $playlistItem->page->component,
                'props' => $playlistItem->content,
                'duration' => $playlistItem->duration,
                'title' => $playlistItem->title ?? '',
            ]),
            'initialScreen' => $screen,
            'initialAnnouncements' => Announcement::all()->toArray(),
            'initialSchedule' => ScheduleEntry::all()->toArray(),
        ]);
    }
}
