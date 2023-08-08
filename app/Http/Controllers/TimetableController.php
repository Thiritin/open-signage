<?php

namespace App\Http\Controllers;

use App\Models\ScheduleEntry;
use Inertia\Inertia;

class TimetableController extends Controller
{
    public function __invoke()
    {
        return Inertia::render('Timetable', [
            'schedule' => ScheduleEntry::with(['room', 'scheduleType'])->get()->values()->toArray(),
        ]);
    }
}
