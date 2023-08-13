<?php

namespace Tests\Feature;

use App\Enums\ResourceOwnership;
use App\Jobs\ScheduleEntryDispatcherJob;
use App\Models\Playlist;
use App\Models\Room;
use App\Models\ScheduleEntry;
use App\Models\Screen;
use Illuminate\Foundation\Testing\RefreshDatabase;
use phpDocumentor\Reflection\Project;
use Tests\TestCase;

class ScheduleEntryAutomationTest extends TestCase
{
    use RefreshDatabase;

    public function test_schedule_entry_automation_on_start()
    {
        $project = \App\Models\Project::factory()->create([
            "path" => "System",
        ]);
        $playlistNormal = Playlist::factory([
            'project_id' => $project->id
        ])->create();
        $playlistAutomation = Playlist::factory([
            'project_id' => $project->id
        ])->create();
        $room = Room::factory()->create();
        $screen = Screen::factory()->create([
            "playlist_id" => $playlistNormal->id,
        ]);

        $scheduleEntry = ScheduleEntry::create([
            "title" => "Test",
            "room_id" => $room->id,
            "flags" => [],
            "starts_at" => now()->subMinutes(5),
            "ends_at" => now()->addMinutes(5),
            "delay" => 0,
            "automation" => [
                [
                    "playlist" => $playlistAutomation->id,
                    "screens" => [$screen->id],
                    "type" => "on_start",
                ]
            ]
        ]);

        $this->assertEquals($screen->playlist_id, $playlistNormal->id);
        ScheduleEntryDispatcherJob::dispatchSync();
        $this->assertEquals($screen->fresh()->playlist_id, $playlistAutomation->id);
    }

    public function test_schedule_entry_automation_skips_emergency()
    {
        $project = \App\Models\Project::factory()->create([
            "path" => "System",
            "type" => ResourceOwnership::EMERGENCY
        ]);
        $playlistNormal = Playlist::factory([
            'project_id' => $project->id
        ])->create();
        $playlistAutomation = Playlist::factory([
            'project_id' => $project->id
        ])->create();
        $room = Room::factory()->create();
        $screen = Screen::factory()->create([
            "playlist_id" => $playlistNormal->id,
        ]);

        $scheduleEntry = ScheduleEntry::create([
            "title" => "Test",
            "room_id" => $room->id,
            "flags" => [],
            "starts_at" => now()->subMinutes(5),
            "ends_at" => now()->addMinutes(5),
            "delay" => 0,
            "automation" => [
                [
                    "playlist" => $playlistAutomation->id,
                    "screens" => [$screen->id],
                    "type" => "on_start",
                ]
            ]
        ]);

        ScheduleEntryDispatcherJob::dispatchSync();
        $this->assertEquals($screen->playlist_id, $playlistNormal->id);
    }


    public function test_schedule_entry_has_run_skips()
    {
        $project = \App\Models\Project::factory()->create([
            "path" => "System",
        ]);
        $playlistNormal = Playlist::factory([
            'project_id' => $project->id
        ])->create();
        $playlistAutomation = Playlist::factory([
            'project_id' => $project->id
        ])->create();
        $room = Room::factory()->create();
        $screen = Screen::factory()->create([
            "playlist_id" => $playlistNormal->id,
        ]);

        $scheduleEntry = ScheduleEntry::create([
            "title" => "Test",
            "room_id" => $room->id,
            "flags" => [],
            "starts_at" => now()->subMinutes(5),
            "ends_at" => now()->addMinutes(5),
            "delay" => 0,
            "automation" => [
                [
                    "playlist" => $playlistAutomation->id,
                    "screens" => [$screen->id],
                    "type" => "on_start",
                    "has_run" => true
                ]
            ]
        ]);

        ScheduleEntryDispatcherJob::dispatchSync();
        $this->assertEquals($screen->playlist_id, $playlistNormal->id);
    }

    public function test_schedule_entry_automation_on_end()
    {
        $project = \App\Models\Project::factory()->create([
            "path" => "System",
        ]);
        $playlistNormal = Playlist::factory([
            'project_id' => $project->id
        ])->create();
        $playlistAutomation = Playlist::factory([
            'project_id' => $project->id
        ])->create();
        $room = Room::factory()->create();
        $screen = Screen::factory()->create([
            "playlist_id" => $playlistNormal->id,
        ]);

        $scheduleEntry = ScheduleEntry::create([
            "title" => "Test",
            "room_id" => $room->id,
            "flags" => [],
            "starts_at" => now()->subMinutes(10),
            "ends_at" => now()->subMinutes(5),
            "delay" => 0,
            "automation" => [
                [
                    "playlist" => $playlistAutomation->id,
                    "screens" => [$screen->id],
                    "type" => "on_end",
                ]
            ]
        ]);

        $this->assertEquals($screen->playlist_id, $playlistNormal->id);
        ScheduleEntryDispatcherJob::dispatchSync();
        $this->assertEquals($screen->fresh()->playlist_id, $playlistAutomation->id);
    }

    public function test_schedule_entry_automation_on_start_with_delay()
    {
        $project = \App\Models\Project::factory()->create([
            "path" => "System",
        ]);
        $playlistNormal = Playlist::factory([
            'project_id' => $project->id
        ])->create();
        $playlistAutomation = Playlist::factory([
            'project_id' => $project->id
        ])->create();
        $room = Room::factory()->create();
        $screen = Screen::factory()->create([
            "playlist_id" => $playlistNormal->id,
        ]);
        $delay = 15;
        $scheduleEntry = ScheduleEntry::create([
            "title" => "Test",
            "room_id" => $room->id,
            "flags" => [],
            "starts_at" => now()->subMinutes($delay + 5),
            "ends_at" => now()->addHour(),
            "delay" => $delay,
            "automation" => [
                [
                    "playlist" => $playlistAutomation->id,
                    "screens" => [$screen->id],
                    "type" => "on_start_with_delay",
                ]
            ]
        ]);

        $this->assertEquals($screen->playlist_id, $playlistNormal->id);
        ScheduleEntryDispatcherJob::dispatchSync();
        $this->assertEquals($screen->fresh()->playlist_id, $playlistAutomation->id);
    }

    public function test_schedule_entry_automation_on_end_with_delay()
    {
        $project = \App\Models\Project::factory()->create([
            "path" => "System",
        ]);
        $playlistNormal = Playlist::factory([
            'project_id' => $project->id
        ])->create();
        $playlistAutomation = Playlist::factory([
            'project_id' => $project->id
        ])->create();
        $room = Room::factory()->create();
        $screen = Screen::factory()->create([
            "playlist_id" => $playlistNormal->id,
        ]);

        $delay = 15;
        $scheduleEntry = ScheduleEntry::create([
            "title" => "Test",
            "room_id" => $room->id,
            "flags" => [],
            "starts_at" => now()->subHour(),
            "ends_at" => now()->subMinutes($delay + 5),
            "delay" => $delay,
            "automation" => [
                [
                    "playlist" => $playlistAutomation->id,
                    "screens" => [$screen->id],
                    "type" => "on_end_with_delay",
                ]
            ]
        ]);

        $this->assertEquals($screen->playlist_id, $playlistNormal->id);
        ScheduleEntryDispatcherJob::dispatchSync();
        $this->assertEquals($screen->fresh()->playlist_id, $playlistAutomation->id);
    }

    public function test_schedule_entry_automation_does_not_start_early_starts_at_with_delay()
    {
        $project = \App\Models\Project::factory()->create([
            "path" => "System",
        ]);
        $playlistNormal = Playlist::factory([
            'project_id' => $project->id
        ])->create();
        $playlistAutomation = Playlist::factory([
            'project_id' => $project->id
        ])->create();
        $room = Room::factory()->create();
        $screen = Screen::factory()->create([
            "playlist_id" => $playlistNormal->id,
        ]);
        $delay = 15;
        $scheduleEntry = ScheduleEntry::create([
            "title" => "Test",
            "room_id" => $room->id,
            "flags" => [],
            "starts_at" => now()->subMinutes($delay - 5),
            "ends_at" => now()->addHour(),
            "delay" => $delay,
            "automation" => [
                [
                    "playlist" => $playlistAutomation->id,
                    "screens" => [$screen->id],
                    "type" => "on_start_with_delay",
                ]
            ]
        ]);

        ScheduleEntryDispatcherJob::dispatchSync();
        $this->assertEquals($screen->playlist_id, $playlistNormal->id);
    }

    public function test_schedule_entry_automation_does_not_start_early_ends_at_with_delay()
    {
        $project = \App\Models\Project::factory()->create([
            "path" => "System",
        ]);
        $playlistNormal = Playlist::factory([
            'project_id' => $project->id
        ])->create();
        $playlistAutomation = Playlist::factory([
            'project_id' => $project->id
        ])->create();
        $room = Room::factory()->create();
        $screen = Screen::factory()->create([
            "playlist_id" => $playlistNormal->id,
        ]);

        $delay = 15;
        $scheduleEntry = ScheduleEntry::create([
            "title" => "Test",
            "room_id" => $room->id,
            "flags" => [],
            "starts_at" => now()->subHour(),
            "ends_at" => now()->subMinutes(5),
            "delay" => $delay,
            "automation" => [
                [
                    "playlist" => $playlistAutomation->id,
                    "screens" => [$screen->id],
                    "type" => "on_end_with_delay",
                ]
            ]
        ]);

        ScheduleEntryDispatcherJob::dispatchSync();
        $this->assertEquals($screen->playlist_id, $playlistNormal->id);
    }
}
