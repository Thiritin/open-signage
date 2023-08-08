<?php

namespace Database\Seeders;

use App\Models\Room;
use App\Models\ScheduleEntry;
use Illuminate\Database\Seeder;

class ScheduleDevSeeder extends Seeder
{
    public function run(): void
    {
        $days = [
            '2023-08-16',
            '2023-08-17',
            '2023-08-18',
            '2023-08-19',
            '2023-08-20',
        ];

        $rooms = [
            'Main Hall' => Room::firstOrCreate(['name' => 'Main Hall'])->id,
            'Gaming Room' => Room::firstOrCreate(['name' => 'Gaming Room'])->id,
            'Entrance' => Room::firstOrCreate(['name' => 'Entrance'])->id,
            'Dining Hall' => Room::firstOrCreate(['name' => 'Dining Hall'])->id,
            'The Zoo' => Room::firstOrCreate(['name' => 'The Zoo'])->id,
        ];

        $scheduleType = \App\Models\ScheduleType::firstOrCreate([
            'name' => 'Organizational Event (Opening, Closing, etc.',
            'color' => '#ff8787', // Light color
        ])->id;

        $schedules = [
            // Wednesday
            [
                'title' => 'Opening Ceremony',
                'room_id' => $rooms['Main Hall'],
                'flags' => '{}',
                'schedule_type_id' => $scheduleType,
                'starts_at' => \Carbon\Carbon::createFromFormat('Y-m-d H:i', "{$days[0]} 18:30", 'Europe/Berlin'),
                'ends_at' => \Carbon\Carbon::createFromFormat('Y-m-d H:i', "{$days[0]} 20:00", 'Europe/Berlin'),
            ],
            [
                'title' => 'House- and EDM-Remixes of 80s and 90s Pop',
                'room_id' => $rooms['Main Hall'],
                'flags' => '{}',
                'schedule_type_id' => $scheduleType,
                'starts_at' => \Carbon\Carbon::createFromFormat('Y-m-d H:i', "{$days[0]} 20:30", 'Europe/Berlin'),
                'ends_at' => \Carbon\Carbon::createFromFormat('Y-m-d H:i', "{$days[0]} 22:00", 'Europe/Berlin'),
            ],
            [
                'title' => '[After Dark] Board Games',
                'room_id' => $rooms['Main Hall'],
                'flags' => '{}',
                'schedule_type_id' => $scheduleType,
                'starts_at' => \Carbon\Carbon::createFromFormat('Y-m-d H:i', "{$days[0]} 22:30", 'Europe/Berlin'),
                'ends_at' => \Carbon\Carbon::createFromFormat('Y-m-d H:i', "{$days[0]} 23:59", 'Europe/Berlin'),
            ],
            [
                'title' => '[After Dark] Horror Panel',
                'room_id' => $rooms['Gaming Room'],
                'flags' => '{}',
                'schedule_type_id' => $scheduleType,
                'starts_at' => \Carbon\Carbon::createFromFormat('Y-m-d H:i', "{$days[0]} 22:00", 'Europe/Berlin'),
                'ends_at' => \Carbon\Carbon::createFromFormat('Y-m-d H:i', "{$days[0]} 23:59", 'Europe/Berlin'),
            ],
            [
                'title' => 'Registration',
                'room_id' => $rooms['Entrance'],
                'flags' => '{}',
                'schedule_type_id' => $scheduleType,
                'starts_at' => \Carbon\Carbon::createFromFormat('Y-m-d H:i', "{$days[0]} 14:00", 'Europe/Berlin'),
                'ends_at' => \Carbon\Carbon::createFromFormat('Y-m-d H:i', "{$days[0]} 17:00", 'Europe/Berlin'),
            ],
            [
                'title' => 'Breakfast',
                'room_id' => $rooms['Dining Hall'],
                'flags' => '{}',
                'schedule_type_id' => $scheduleType,
                'starts_at' => \Carbon\Carbon::createFromFormat('Y-m-d H:i', "{$days[0]} 10:00", 'Europe/Berlin'),
                'ends_at' => \Carbon\Carbon::createFromFormat('Y-m-d H:i', "{$days[0]} 11:30", 'Europe/Berlin'),
            ],
            [
                'title' => 'Dinner',
                'room_id' => $rooms['Dining Hall'],
                'flags' => '{}',
                'schedule_type_id' => $scheduleType,
                'starts_at' => \Carbon\Carbon::createFromFormat('Y-m-d H:i', "{$days[0]} 17:00", 'Europe/Berlin'),
                'ends_at' => \Carbon\Carbon::createFromFormat('Y-m-d H:i', "{$days[0]} 18:00", 'Europe/Berlin'),
            ],
            // Thursday
            [
                'title' => 'Your First Furry Con',
                'room_id' => $rooms['Main Hall'],
                'flags' => '{}',
                'schedule_type_id' => $scheduleType,
                'starts_at' => \Carbon\Carbon::createFromFormat('Y-m-d H:i', "{$days[1]} 12:00", 'Europe/Berlin'),
                'ends_at' => \Carbon\Carbon::createFromFormat('Y-m-d H:i', "{$days[1]} 13:00", 'Europe/Berlin'),
            ],
            // Add more events here...
            [
                'title' => 'DD Setup',
                'room_id' => $rooms['Main Hall'],
                'flags' => '{}',
                'schedule_type_id' => $scheduleType,
                'starts_at' => \Carbon\Carbon::createFromFormat('Y-m-d H:i', "{$days[3]} 11:00", 'Europe/Berlin'),
                'ends_at' => \Carbon\Carbon::createFromFormat('Y-m-d H:i', "{$days[3]} 12:00", 'Europe/Berlin'),
            ],
            // Add more events for Saturday...
            // Sunday
            [
                'title' => 'Expand your Fandom',
                'room_id' => $rooms['Main Hall'],
                'flags' => '{}',
                'schedule_type_id' => $scheduleType,
                'starts_at' => \Carbon\Carbon::createFromFormat('Y-m-d H:i', "{$days[4]} 11:00", 'Europe/Berlin'),
                'ends_at' => \Carbon\Carbon::createFromFormat('Y-m-d H:i', "{$days[4]} 12:30", 'Europe/Berlin'),
            ],
        ];
        ScheduleEntry::upsert($schedules, ['title', 'room_id', 'schedule_type_id', 'starts_at', 'ends_at'], ['title', 'room_id', 'schedule_type_id', 'starts_at', 'ends_at']);
    }
}
