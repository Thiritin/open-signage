<?php

namespace App\Http\Controllers;

use App\Models\ScheduleEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Pool;

use Inertia\Inertia;

class EurofurenceScheduleController extends Controller
{
    public function __invoke()
    {

        $responses = Http::pool(fn (Pool $pool) => [
            $pool->timeout(5)->retry(3, 500)->acceptJson()->get("https://6al.de/efsched/geteventjson"),
            $pool->timeout(5)->retry(3, 500)->acceptJson()->get("https://6al.de/efsched/getconnews")
        ]);

        //geteventjson get the whole pre-planed ef event schedule should not be changed during the con, can be checked only once a while
        //getconnews represents the official announcement channel and should be checked frequently,
        //for new, delayed or cancelled events (if the news has a relation to a planed schedule event, the data section contains that corresponding event_id)

        return Inertia::render('EurofurenceSchedule', [
            'schedule' => ScheduleEntry::with(['room', 'scheduleType'])->orderBy('starts_at')->get()->values()->toArray(),
            'events' => $responses[0]->json(),
            'connews' => $responses[1]->json(),
        ]);
    }
}
