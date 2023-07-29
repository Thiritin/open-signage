<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\PlaylistItem;
use App\Models\ScheduleEntry;
use App\Models\Screen;
use Inertia\Inertia;

class ScreenController extends Controller
{
    public function __invoke(Screen $screen)
    {
        return Inertia::render('../Main', [
            'initialPages' => $screen->playlist->playlistItems->map(fn(PlaylistItem $playlistItem) => [
                'layout' => $playlistItem->layout->component,
                'component' => $playlistItem->page->component,
                'props' => $playlistItem->content,
                'duration' => $playlistItem->duration,
                'title' => $playlistItem->title ?? '',
            ]),
            'screen' => $screen,
            'initialAnnouncements' => Announcement::all()->toArray(),
            'initialSchedule' => ScheduleEntry::all()->toArray(),
        ]);
    }
}
