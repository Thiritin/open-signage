<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\Project;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        $project = Project::firstWhere('path','wt23');
        $pages = [
            [
                "name" => "Announcement",
                "component" => "Announcement",
                "project_id" => $project->id,
            ],
            [
                "name" => "Just Logo",
                "component" => "Logo",
                "project_id" => $project->id,
            ],
            [
                "name" => "Logo with Time",
                "component" => "LogoText",
                "project_id" => $project->id,
            ],
            [
                "name" => "ScheduleToday",
                "component" => "ScheduleToday",
                "project_id" => $project->id,
            ]
        ];
        Page::upsert($pages, ['component'], ['schema','project_id']);
    }
}
