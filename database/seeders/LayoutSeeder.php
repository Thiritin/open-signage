<?php

namespace Database\Seeders;

use App\Models\Layout;
use Illuminate\Database\Seeder;

class LayoutSeeder extends Seeder
{
    public function run(): void
    {
        $layouts = [
            [
                "name" => "None",
                "component" => "None",
            ],
            [
                "name" => "Full Screen",
                "component" => "FullScreen",
            ],
            [
                "name" => "Primary Layout",
                "component" => "PrimaryLayout",
            ]
        ];
        Layout::upsert($layouts, ['component']);
    }
}
