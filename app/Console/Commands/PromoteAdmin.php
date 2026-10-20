<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class PromoteAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * Usage:
     *   php artisan admin:promote user@example.com
     *   php artisan admin:promote user@example.com --revoke
     */
    protected $signature = 'admin:promote
        {email : Email address of the user to promote}
        {--revoke : Remove admin access instead of granting it}';

    /**
     * The console command description.
     */
    protected $description = 'Grant or revoke admin dashboard access for a user by email.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $email = strtolower(trim((string) $this->argument('email')));

        $user = User::query()
            ->whereRaw('LOWER(TRIM(email)) = ?', [$email])
            ->first();

        if (!$user) {
            $this->error("No user found with email: {$email}");

            return self::FAILURE;
        }

        $revoke = (bool) $this->option('revoke');

        $user->update([
            'is_admin' => !$revoke,
        ]);

        if ($revoke) {
            $this->info("{$user->email} is no longer an admin.");
        } else {
            $this->info("{$user->email} is now an admin. They can access /admin after logging in.");
        }

        return self::SUCCESS;
    }
}
