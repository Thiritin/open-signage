<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            [
                "name" => "Announcement",
                "component" => "Announcement",
            ],
            [
                "name" => "Just Logo",
                "component" => "Logo",
            ],
            [
                "name" => "Logo with Time",
                "component" => "LogoText",
            ],
            [
                "name" => "ScheduleToday",
                "component" => "ScheduleToday",
            ],
            [
                "name" => "Screen Identification",
                "component" => "ScreenIdentification",
            ]
        ];
        Page::upsert($pages, ['component'], ['schema']);
    }
}
