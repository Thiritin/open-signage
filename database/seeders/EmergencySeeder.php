<?php

namespace Database\Seeders;

use App\Enums\ResourceOwnership;
use App\Models\Layout;
use App\Models\Page;
use Illuminate\Database\Seeder;

class EmergencySeeder extends Seeder
{
    public function run(): void
    {
        $alertPage = Page::updateOrCreate([
            'name' => 'EmergencyAlert',
            'type' => ResourceOwnership::EMERGENCY,
            'component' => 'EmergencyAlert'
        ]);

        $alertLayout = Layout::updateOrCreate([
            'name' => 'EmergencyLayout',
            'type' => ResourceOwnership::EMERGENCY,
            'component' => 'EmergencyLayout'
        ]);
    }
}
