<?php

namespace App\Jobs;

use App\Events\UpdateAnnouncementEvent;
use App\Events\UpdateScheduleEvent;
use App\Models\Announcement;
use App\Models\Room;
use App\Models\ScheduleEntry;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\Pool;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SyncEurofurenceScheduleJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
    }

    public function handle(): void
    {
        // geteventjson get the whole pre-planed ef event schedule should not be changed during the con, can be checked only once a while
        $efScheduleJson = Http::get('https://6al.de/efsched/geteventjson')->json();

        // getconnews represents the official announcement channel and should be checked frequently, contains delays of events.
        $efConNewsJson = Http::get('https://6al.de/efsched/getconnews')->json();

        /**
         * Order Schedule
         */
        $efSchedule = $this->getEfSchedule($efScheduleJson);

        /**
         * Sync Schedule
         */
        $rooms = $this->scheduleRooms($efSchedule);

        /**
         * Insert Schedule Events
         */
        $data = $efSchedule->map(fn($schedule) => [
            "id" => $schedule['event_id'],
            "room_id" => $rooms[$this->getConferenceRoomString($schedule['conference_room'])]['id'],
            "starts_at" => $schedule['start_time'],
            "ends_at" => $schedule['end_time'],
            "title" => $schedule['title'],
            "flags" => "{}",
            "automation" => "{}"
        ])->toArray();
        ScheduleEntry::upsert($data, "id", ["room_id", "starts_at", "ends_at", "title"]);

        /**
         * Announcements
         */
        $conNews = $this->getEfAnnouncements($efConNewsJson);
        $knownAnnouncementIds = Announcement::whereIn('id', $conNews->pluck('id'))->pluck('id');
        $newAnnouncements = $conNews->reject(fn($announcement) => $knownAnnouncementIds->contains($announcement['id']));
        $newAnnouncements->filter(fn($v) => isset($v['delay']))
            ->each(function ($announcement) {
                ScheduleEntry::where('id', $announcement['event_id'])
                    ->increment('delay', $announcement['delay']);
            });

        Announcement::upsert($conNews->map(fn($announcement) => [
            "id" => $announcement['id'],
            "title" => $announcement['title'],
            "content" => $announcement['content'],
            "starts_at" => $announcement['starts_at'] ?? now(),
            "ends_at" => $announcement['ends_at'] ?? now(),
        ])->toArray(), "id", ["title", "content", "starts_at", "ends_at"]);

        if ($newAnnouncements->count() > 0) {
            broadcast(new UpdateAnnouncementEvent());
            broadcast(new UpdateScheduleEvent());
        }
    }

    private function scheduleRooms($efSchedule)
    {
        $roomData = $efSchedule->pluck('conference_room')->unique()->map(function ($conferenceRoom) use ($efSchedule) {
            $rooms = Str::of($conferenceRoom)
                ->split("/[‑–—‐−‐–—⸺|‖•‣]/")
                ->reject(fn($state) => empty($state))
                ->map(fn($state) => Str::of($state)->replaceMatches('/\s+/', ' ')->trim()->toString())
                ->values()
                ->toArray();

            return [
                'external_name' => $this->getConferenceRoomString($conferenceRoom),
                'name' => $rooms[0],
                'venue_name' => $rooms[1] ?? $rooms[0],
            ];
        })->toArray();
        Room::upsert($roomData, "external_name", ["name", "venue_name"]);
        return Room::all()->keyBy('external_name')->toArray();
    }

    /**
     * @param $conferenceRoom
     * @return string
     */
    private function getConferenceRoomString($conferenceRoom): string
    {
        return Str::of($conferenceRoom)->replaceMatches('/\s+/', ' ')->lower()->trim()->slug()->toString();
    }

    /**
     * @param  mixed  $efScheduleJson
     * @return mixed
     */
    public function getEfSchedule(mixed $efScheduleJson)
    {
        $efSchedule = collect($efScheduleJson)
            ->map(function ($efScheduleEntry) {
                $day = Carbon::parse($efScheduleEntry['day']);
                $startTime = Carbon::parse($efScheduleEntry['start_time'])->setDateFrom($day);
                $endTime = Carbon::parse($efScheduleEntry['end_time'])->setDateFrom($day);
                $endTime->lt($startTime) && $endTime->addDay();
                unset($efScheduleEntry['description']);
                return [
                    ...$efScheduleEntry,
                    "title" => Str::of($efScheduleEntry['title'])->replaceMatches('/\s+/', ' ')->trim()->toString(),
                    "conference_room" => Str::of($efScheduleEntry['conference_room'])->replaceMatches('/\s+/',
                        ' ')->trim()->toString(),
                    "start_time" => $startTime,
                    "end_time" => $endTime,
                ];
            })
            ->groupBy(function ($efScheduleEntry) {
                return Str::slug($efScheduleEntry['title'].'-'.$efScheduleEntry['conference_room']);
            })
            // Merge events that are directly after each other
            ->flatMap(function ($efScheduleEntries) {
                $eventGroup = collect($efScheduleEntries)->sortBy('start_time')->values()->toArray();

                $merged = [];
                $currentEvent = null;

                foreach ($eventGroup as $event) {
                    if (!$currentEvent) {
                        $currentEvent = $event;
                        continue;
                    }

                    // Check if the current event's end time matches the next event's start time
                    if ($currentEvent['end_time']->eq($event['start_time'])) {
                        $currentEvent['end_time'] = $event['end_time'];
                    } else {
                        $merged[] = $currentEvent;
                        $currentEvent = $event;
                    }
                }

                if ($currentEvent) {
                    $merged[] = $currentEvent;
                }

                return $merged;
            })
            ->sortBy('start_time');
        return $efSchedule;
    }

    public function getEfAnnouncements($efConNews)
    {
        return collect($efConNews)
            ->map(function ($efConNewsEntry) {
                $data = [
                    "id" => $efConNewsEntry['id'],
                    "title" => $efConNewsEntry['news']['title'],
                    "content" => $efConNewsEntry['news']['message'],
                    "starts_at" => Carbon::createFromTimestamp($efConNewsEntry['date']),
                    "ends_at" => Carbon::createFromTimestamp($efConNewsEntry['news']['valid_until']),
                ];

                if (isset($efConNewsEntry['data']['event_id'])) {
                    $data['event_id'] = $efConNewsEntry['data']['event_id'];
                }

                if (isset($efConNewsEntry['data']['delay'])) {
                    $data['delay'] = $efConNewsEntry['data']['delay'];
                }

                return $data;
            })
            ->sortBy('start_time');

    }
}
