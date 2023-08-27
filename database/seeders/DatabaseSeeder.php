<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
        $this->call(EurofurenceSeeder::class);

        if (App::isLocal()) {
            User::firstOrCreate([
                'name' => 'Admin',
            ], [
                'name' => 'Admin',
                'email' => 'me@thiritin.com',
                'password' => Hash::make('password'),
            ]);
        }
    }
}
