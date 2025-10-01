<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class UpdateUserPasswords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-user-passwords';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update existing users with default passwords';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Updating user passwords...');

        $users = User::whereNull('password')->get();

        foreach ($users as $user) {
            if ($user->email === 'admin@example.com') {
                $user->password = bcrypt('admin123');
            } else {
                $user->password = bcrypt('password');
            }
            $user->save();
            $this->info("Updated password for: {$user->name} ({$user->email})");
        }

        $this->info('User passwords updated successfully!');
        $this->info('Default credentials:');
        $this->info('- Admin: admin@example.com / admin123');
        $this->info('- Others: [email] / password');
    }
}
