<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Playlist;
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
        Playlist::updateOrCreate([
            'name' => 'Default'
        ]);

        if(App::isLocal() === false) {
            return;
        }

        User::updateOrCreate([
            'name' => 'Admin',
            'email' => 'me@thiritin.com',
            'password' => Hash::make('password')
        ]);
        Screen::factory(10)->create();
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
