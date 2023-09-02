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
            'blabla' => [
                [
                    "test" => 1,
                    "te5st" => 2,
                ],
                [
                    "test" => 4,
                    "t2est" => 5,
                ],
            ]
        ]);
    }
}
