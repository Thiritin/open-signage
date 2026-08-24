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

        // Generic "Starter" demo (screen at /screens/demo).
        $this->call(StarterSeeder::class);

        // Legacy / event-specific seeders. These are no longer auto-run now
        // that the app is a generic framework, but the seeder files are kept
        // and can still be run manually, e.g.:
        //   php artisan db:seed --class=WildTimesSeeder
        // $this->call(WildTimesSeeder::class);
        // $this->call(EurofurenceSeeder::class);
        // $this->call(FurcietySeeder::class);

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
