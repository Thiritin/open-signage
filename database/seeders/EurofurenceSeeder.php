<?php

namespace Database\Seeders;

use App\Models\Project;
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
            'path' => 'EF27',
            'name' => 'Eurofurence 27',
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
            'schema' => [],
        ]);
    }
}
