<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Layout;
use App\Models\Page;
use App\Models\Playlist;
use App\Models\PlaylistItem;
use App\Models\Screen;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Playlist::create([
            'name' => 'Default'
        ]);

        Page::updateOrCreate([
            'name' => 'Screen Identification',
            'component' => 'ScreenIdentification'
        ]);

        Layout::updateOrCreate([
            'name' => 'None',
            'component' => 'None'
        ]);

        PlaylistItem::create([
            'playlist_id' => 1,
            'page_id' => 1,
            'layout_id' => 1,
            'duration' => 5,
            'content' => [
                'screen' => 'Test Message'
            ]
        ]);

        if (App::isLocal() === false) {
            return;
        }

        User::updateOrCreate([
            'name' => 'Admin',
            'email' => 'me@thiritin.com',
            'password' => Hash::make('password')
        ]);
        Screen::create([
            "name" => "Test Screen",
            "playlist_id" => 1
        ]);
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
