<?php

namespace Database\Seeders;

use App\Models\Project;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Seeder;

class WildTimesSeeder extends Seeder
{
    public function run(): void
    {
        $project = Project::firstOrCreate([
            'path' => 'WT23',
            'name' => 'Wild Times 23',
        ]);

        $announcementPage = $project->pages()->updateOrCreate([
            'component' => 'Announcement',
        ], [
            'name' => 'Announcement',
            'component' => 'Announcement',
        ]);

        $splashPage = $project->pages()->updateOrCreate([
            'component' => 'LogoText',
        ], [
            'name' => 'Logo with Time',
            'component' => 'LogoText',
        ]);

        $schedulePage = $project->pages()->updateOrCreate([
            'component' => 'ScheduleToday',
        ], [
            'name' => 'ScheduleToday',
            'component' => 'ScheduleToday',
            'schema' => [
                [
                    "name" => "showAnnouncements",
                    "property" => "showAnnouncements",
                    "type" => "Checkbox"
                ],
                [
                    "name" => "showSchedule",
                    "property" => "showSchedule",
                    "type" => "Checkbox"
                ],
                [
                    "name" => "showDate",
                    "property" => "showDate",
                    "type" => "DatePicker"
                ],
                [
                    "name" => "showToday",
                    "property" => "showToday",
                    "type" => "Checkbox"
                ]
            ]
        ]);

        $centeredLayout = $project->layouts()->firstOrCreate([
            'name' => 'Centered',
            'component' => 'Centered',
        ]);

        $primaryLayout = $project->layouts()->firstOrCreate([
            'name' => 'Primary Layout',
            'component' => 'PrimaryLayout',
        ]);

        /**
         * Playlists
         */
        $playlists = [
            [
                'name' => 'Splash Screen (Logo and Time)',
                'pages' => [
                    [
                        'title' => 'Logo with Time',
                        'layout_id' => $centeredLayout->id,
                        'page_id' => $splashPage->id,
                    ],
                ],
            ]
        ];

        collect($playlists)->each(function ($data) use ($project) {
            $playlist = $project->playlists()->firstOrCreate([
                'name' => $data['name'],
            ]);
            collect($data['pages'])->each(fn($page) => $playlist->playlistItems()->firstOrCreate([
                'title' => $page['title'],
            ], [
                'layout_id' => $page['layout_id'],
                'page_id' => $page['page_id'],
                'duration' => $page['duration'] ?? 0,
            ]));
        });
    }
}
