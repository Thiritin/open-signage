<?php

namespace Database\Seeders;

use App\Enums\ResourceOwnership;
use App\Models\Project;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $projects = [
            [
                'name' => 'System',
                'type' => ResourceOwnership::SYSTEM,
                'path' => 'system' // DO NOT CHANGE PATH
            ],
            [
                'name' => 'Wild Times 2023',
                'type' => ResourceOwnership::USER,
                'path' => 'wt23'
            ],
            [
                'name' => 'Eurofurence 27',
                'type' => ResourceOwnership::USER,
                'path' => 'ef27'
            ],
        ];
        Project::upsert($projects, ['path']);
    }
}
