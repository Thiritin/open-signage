<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class UserChangePasswordCommand extends Command
{
    protected $signature = 'user:change-password';

    protected $description = 'Changes user password';

    public function handle(): void
    {
        $email = $this->ask('What is the email of the user?');
        $user = User::where('email', $email)->first();
        if (!$user) {
            $this->error("User with email {$email} was not found");
            return;
        }
        $password = $this->secret('Please, enter the new password for the user');
        $user->password = Hash::make($password);
        $user->save();
        $this->info("Password for user {$user->name} <{$user->email}> was changed");
    }
}
