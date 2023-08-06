<?php

namespace Database\Seeders;

use App\Enums\ResourceOwnership;
use App\Models\Layout;
use App\Models\Page;
use App\Models\Project;
use Illuminate\Database\Seeder;

class EmergencySeeder extends Seeder
{
    public function run(): void
    {
        $project = Project::updateOrCreate(
            [
                'path' => 'emergency',
                'type' => ResourceOwnership::EMERGENCY,
            ],
            [
                'name' => 'Emergency',
                'type' => ResourceOwnership::EMERGENCY,
                'path' => 'Emergency' // DO NOT CHANGE PATH
            ]);

        $alertPage = Page::updateOrCreate([
            'component' => 'EmergencyAlert',
        ],[
            'name' => 'EmergencyAlert',
            'component' => 'EmergencyAlert',
            'project_id' => $project->id
        ]);

        $alertLayout = Layout::updateOrCreate([
            'component' => 'EmergencyLayout',
        ],[
            'component' => 'EmergencyLayout',
            'name' => 'EmergencyLayout',
            'project_id' => $project->id
        ]);
    }
}
