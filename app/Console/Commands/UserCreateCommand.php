<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class UserCreateCommand extends Command
{
    protected $signature = 'user:create {name?} {email?} {--admin}';

    protected $description = 'Asks user about name and email and creates a new user';

    public function handle(): void
    {
        // If name is not provided as an argument, ask user about it
        $name = $this->argument('name') ?? $this->ask('What is your name?');
        // If email is not provided as an argument, ask user about it
        $email = $this->argument('email') ?? $this->ask('What is your email?');
        $admin = $this->option('admin');
        // Ask about the password
        $password = $this->secret('Please, enter the password for the new user');

        // Create the user
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        $this->info("User {$user->name} <{$user->email}> was created");



    }
}
