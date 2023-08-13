<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Artwork;
use App\Models\PlaylistItem;
use App\Models\ScheduleEntry;
use App\Models\Screen;
use App\Settings\GeneralSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ScreenController extends Controller
{
    public function __invoke(Request $request, $slug = null)
    {
        $finalSlug = $slug ?? $request->get('kiosk') ?? null;
        abort_if(is_null($finalSlug), 400, 'No slug provided');

        $screen = Screen::firstOrCreate(['slug' => $finalSlug], [
            'name' => 'New Screen '.$finalSlug,
            'slug' => $finalSlug,
            'playlist_id' => app(GeneralSettings::class)->playlist_id,
            'provisioned' => false,
        ]);

        return Inertia::render('Main', [
            'initialPages' => $screen->playlist->playlistItems
                ->reject(fn(PlaylistItem $playlistItem) => $playlistItem->is_active === false)
                ->map(fn(PlaylistItem $playlistItem) => [
                    'layout' => [
                        'component' => $playlistItem->layout->component,
                        'path' => $playlistItem->layout->project->path,
                    ],
                    'path' => $playlistItem->page->project->path,
                    'component' => $playlistItem->page->component,
                    'props' => $playlistItem->parsedContent(),
                    'duration' => $playlistItem->duration,
                    'title' => $playlistItem->title ?? '',
                    'starts_at' => $playlistItem->starts_at,
                    'ends_at' => $playlistItem->ends_at,
                ])->values(),
            'initialScreen' => $screen,
            'initialArtworks' => Artwork::all()->map(fn(Artwork $artwork) => [
                'id' => $artwork->id,
                'name' => $artwork->name,
                'artist' => $artwork->artist,
                'horizontal' => (empty($artwork->file_horizontal)) ? null : Storage::disk('public')->url($artwork->file_horizontal),
                'vertical' => (empty($artwork->file_vertical)) ? null : Storage::disk('public')->url($artwork->file_vertical),
                'banner' => (empty($artwork->file_banner)) ? null : Storage::disk('public')->url($artwork->file_banner),
            ])->toArray(),
            'initialAnnouncements' => Announcement::all()->toArray(),
            'initialSchedule' => ScheduleEntry::with(['room', 'scheduleType'])->orderBy('starts_at')->get()->toArray(),
        ]);
    }
}
