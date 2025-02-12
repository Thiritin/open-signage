<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

class FurcietySeeder extends Seeder
{
    public function run(): void
    {
        $project = Project::firstOrCreate([
            'path' => 'FURCIETY2025',
            'name' => 'Furcienty 2025',
        ]);

        $announcementPage = $project->pages()->updateOrCreate([
            'component' => 'Announcement',
        ], [
            'name' => 'Announcement',
            'component' => 'Announcement',
            'schema' => [
                [
                    "name" => "Header",
                    "property" => "title",
                    "type" => "TextInput"
                ],
                [
                    "name" => "Text",
                    "property" => "text",
                    "type" => "RichEditor"
                ],
                [
                    "name" => "centerContent",
                    "property" => "centerContent",
                    "type" => "Checkbox"
                ],
                [
                    "name" => "Use Container",
                    "property" => "useContainer",
                    "type" => "Checkbox"
                ],
                [
                    "name" => "Image",
                    "property" => "image",
                    "type" => "ImageInput"
                ],
                [
                    "name" => "Text Color",
                    "property" => "textColor",
                    "type" => "ColorPicker"
                ],
                [
                    "name" => "Header Size",
                    "property" => "headerSize",
                    "type" => "Select",
                    "options" => [
                        "text-3xl" => "text-3xl",
                        "text-4xl" => "text-4xl",
                        "text-5xl" => "text-5xl",
                        "text-6xl" => "text-6xl",
                        "text-7xl" => "text-7xl",
                        "text-8xl" => "text-8xl",
                        "text-9xl" => "text-9xl",
                        "text-10xl" => "text-10xl",
                        "text-11xl" => "text-11xl",
                        "text-12xl" => "text-12xl",
                        "text-13xl" => "text-13xl",
                        "text-14xl" => "text-14xl",
                    ]
                ],
                [
                    "name" => "Text Size",
                    "property" => "textSize",
                    "type" => "Select",
                    "options" => [
                        "text-3xl" => "text-3xl",
                        "text-4xl" => "text-4xl",
                        "text-5xl" => "text-5xl",
                        "text-6xl" => "text-6xl",
                        "text-7xl" => "text-7xl",
                        "text-8xl" => "text-8xl",
                        "text-9xl" => "text-9xl",
                        "text-10xl" => "text-10xl",
                        "text-11xl" => "text-11xl",
                        "text-12xl" => "text-12xl",
                        "text-13xl" => "text-13xl",
                        "text-14xl" => "text-14xl",
                    ]
                ]
            ]
        ]);

        $splashPage = $project->pages()->updateOrCreate([
            'component' => 'LogoText',
        ], [
            'name' => 'Logo with Time',
            'component' => 'LogoText',
        ]);

        $timetablePage = $project->pages()->updateOrCreate([
            'component' => 'TimetablePage',
        ], [
            'name' => 'Timetable',
            'component' => 'TimetablePage',
            'schema' => [
                [
                    "name" => "showDate",
                    "property" => "showDate",
                    "type" => "DatePicker"
                ],
                [
                    "name" => "showItems",
                    "property" => "showItems",
                    "type" => "TextInput"
                ],
                [
                    "name" => "carousel",
                    "property" => "carousel",
                    "type" => "Checkbox"
                ],
                [
                    "name" => "autoplay",
                    "property" => "autoplay",
                    "type" => "TextInput"
                ],
                [
                    "name" => "heightFactor",
                    "property" => "heightFactor",
                    "type" => "TextInput"
                ]
            ]
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
