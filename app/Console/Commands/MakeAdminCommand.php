<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class MakeAdminCommand extends Command
{
    protected $signature = 'station:make-admin {email : The email address}';

    protected $description = 'Create or promote a user to platform admin';

    public function handle(): int
    {
        $email = $this->argument('email');
        $user = User::where('email', $email)->first();

        if ($user) {
            $user->update(['is_platform_admin' => true]);
            $this->info("User {$email} promoted to platform admin.");

            return self::SUCCESS;
        }

        $password = null;
        while (empty($password)) {
            $password = $this->secret('Password for new user');
            if (empty($password)) {
                $this->error('Password cannot be empty.');
            }
        }

        $name = $this->ask('Name', 'Admin');

        User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'is_platform_admin' => true,
        ]);

        $this->info("Admin user {$email} created.");

        return self::SUCCESS;
    }
}
