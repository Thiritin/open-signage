<?php

namespace App\Http\Controllers;

use App\Models\ScheduleEntry;
use Inertia\Inertia;

class TimetableController extends Controller
{
    public function __invoke()
    {
        return Inertia::render('Timetable', [
            'schedule' => ScheduleEntry::with('room')->get()
                ->groupBy(fn ($entry) => $entry->starts_at->format('Y-m-d'))
                ->sortBy(fn ($entries, $date) => $date)
                ->map(fn ($room) => collect($room)->sortBy('starts_at')->groupBy('room.name')->values())
                ->values()
                ->toArray(),
        ]);
    }
}
