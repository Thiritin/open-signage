<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

class WildTimesSeeder extends Seeder
{
    public function run(): void
    {
        Project::where('path', 'wt23')->delete();

        $project = Project::firstOrCreate([
            'path' => 'WT23',
            'name' => 'Wild Times 23',
        ]);

        $announcementPage = $project->pages()->firstOrCreate([
            'name' => 'Announcement',
            'component' => 'Announcement',
        ]);

        $splashPage = $project->pages()->firstOrCreate([
            'name' => 'Logo with Time',
            'component' => 'LogoText',
        ]);

        $schedulePage = $project->pages()->firstOrCreate([
            'name' => 'ScheduleToday',
            'component' => 'ScheduleToday',
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
        $playlists = [[
            'name' => 'Splash Screen (Logo and Time)',
            'pages' => [
                [
                    'title' => 'Logo with Time',
                    'layout_id' => $centeredLayout->id,
                    'page_id' => $splashPage->id,
                ],
            ],
        ]];

        collect($playlists)->each(function ($data) use ($project) {
            $playlist = $project->playlists()->firstOrCreate([
                'name' => $data['name'],
            ]);
            collect($data['pages'])->each(fn ($page) => $playlist->playlistItems()->firstOrCreate([
                'title' => $page['title'],
            ], [
                'layout_id' => $page['layout_id'],
                'page_id' => $page['page_id'],
                'duration' => $page['duration'] ?? 0,
            ]));
        });
    }
}
