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
        $this->call(EmergencySeeder::class);
        $this->call(SystemSeeder::class);

        // Wild Times Seeder
        $this->call(WildTimesSeeder::class);

        if (App::isLocal()) {
            User::firstOrCreate([
                'name' => 'Admin',
            ], [
                'name' => 'Admin',
                'email' => 'me@thiritin.com',
                'password' => Hash::make('password')
            ]);
        }
    }
}
