<?php

namespace App\Http\Controllers;

use App\Models\ScheduleEntry;
use Inertia\Inertia;

class TimetableController extends Controller
{
    public function __invoke()
    {
        return Inertia::render('Timetable', [
            'initialSchedule' => ScheduleEntry::with(['room', 'scheduleType','scheduleOrganizer'])->orderBy('starts_at')->get()->values()->toArray(),
        ]);
    }
}
