<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class MakeUserAdmin extends Command
{
    protected $signature = 'user:make-admin {email? : The email address of the user}';
    protected $description = 'Make a user an admin';

    public function handle()
    {
        $email = $this->argument('email');

        if (!$email) {
            // Show all users
            $users = User::all(['id', 'name', 'email', 'role']);
            $this->table(['ID', 'Name', 'Email', 'Role'], $users->map(function ($user) {
                return [$user->id, $user->name, $user->email, $user->role ?? 'user'];
            }));

            $email = $this->ask('Enter the email address of the user to make admin');
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User with email {$email} not found.");
            return 1;
        }

        $user->role = 'admin';
        $user->save();

        $this->info("User {$user->name} ({$user->email}) is now an admin!");
        return 0;
    }
}
