<?php

namespace Database\Seeders;

use App\Enums\ResourceOwnership;
use App\Models\Layout;
use App\Models\Page;
use App\Models\Playlist;
use App\Models\Project;
use Illuminate\Database\Seeder;

class SystemSeeder extends Seeder
{
    public function run(): void
    {
        $project = Project::updateOrCreate([
            'path' => 'system',
        ], [
            'name' => 'System',
            'path' => 'system',
            'type' => ResourceOwnership::SYSTEM,
        ]);

        $page = Page::updateOrCreate([
            "component" => "ScreenIdentification",
            "project_id" => $project->id,
        ],[
            "name" => "Screen Identification",
            "component" => "ScreenIdentification",
            "project_id" => $project->id,
        ]);

        $layout = Layout::updateOrCreate([
            "component" => "None",
            "project_id" => $project->id,
        ],[
            "name" => "None",
            "project_id" => $project->id,
            "component" => "None",
        ]);

        $playlist = Playlist::firstOrCreate([
            'name' => 'Screen Identification',
            'project_id' => $project->id,
        ]);
        $playlist->playlistItems()->updateOrCreate([
            'page_id' => $page->id,
            'layout_id' => $layout->id,
            'duration' => 10,
        ]);
    }
}
