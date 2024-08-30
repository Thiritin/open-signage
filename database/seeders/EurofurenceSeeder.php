<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EurofurenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $project = Project::firstOrCreate([
            'path' => 'EF28',
            'name' => 'Eurofurence 28',
        ]);

        // Create Primary Layout
        $layout = $project->layouts()->updateOrCreate([
            "component" => "PrimaryLayout",
        ],[
            'name' => 'Primary',
            "component" => "PrimaryLayout",
        ]);

        $page = $project->pages()->updateOrCreate([
            'component' => "Signpost",
        ],[
            'name' => "Signpost",
            'component' => "Signpost",
            'schema' => [
                [
                    "name" => "Page Switch (ms)",
                    "property" => "pageSwitchingTimer",
                    "type" => "TextInput"
                ],
            ],
        ]);

        $project->pages()->updateOrCreate([
            'component' => "IntegratedSignCCH",
        ],[
            'name' => "IntegratedSignCCH",
            'component' => "IntegratedSignCCH",
            'schema' => [
                [
                    "name" => "Page Switch (ms)",
                    "property" => "pageSwitchingTimer",
                    "type" => "TextInput"
                ],
            ],
        ]);

        $project->pages()->updateOrCreate([
            'component' => "SingleRoom",
        ],[
            'name' => "SingleRoom",
            'component' => "SingleRoom",
            'schema' => [
                [
                    "name" => "Show Room Name",
                    "property" => "showRoomName",
                    "type" => "Checkbox"
                ],
            ],
        ]);

        // Schedule
        $project->pages()->updateOrCreate([
            'component' => "Schedule",
        ],[
            'name' => "Schedule",
            'component' => "Schedule",
            'schema' => [
                [
                    "name" => "Page Switch (ms)",
                    "property" => "pageSwitchingTimer",
                    "type" => "TextInput"
                ],
                [
                    "name" => "Use Theme Font",
                    "property" => "isThemeFont",
                    "type" => "Checkbox"
                ],
            ],
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
    }
}
