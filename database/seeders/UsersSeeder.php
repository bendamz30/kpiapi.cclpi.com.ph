<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'userId' => 103,
                'name' => 'John Smith',
                'email' => 'john.smith@example.com',
                'passwordHash' => password_hash('cclpi', PASSWORD_DEFAULT),
                'role' => 'RegionalUser',
                'regionId' => 1,
                'areaId' => 1,
                'salesTypeId' => 1,
            ],
            [
                'userId' => 104,
                'name' => 'Sarah Johnson',
                'email' => 'sarah.johnson@example.com',
                'passwordHash' => password_hash('cclpi', PASSWORD_DEFAULT),
                'role' => 'RegionalUser',
                'regionId' => 2,
                'areaId' => 2,
                'salesTypeId' => 2,
            ],
            [
                'userId' => 105,
                'name' => 'Mike Davis',
                'email' => 'mike.davis@example.com',
                'passwordHash' => password_hash('cclpi', PASSWORD_DEFAULT),
                'role' => 'RegionalUser',
                'regionId' => 3,
                'areaId' => 3,
                'salesTypeId' => 1,
            ],
            [
                'userId' => 106,
                'name' => 'Lisa Wilson',
                'email' => 'lisa.wilson@example.com',
                'passwordHash' => password_hash('cclpi', PASSWORD_DEFAULT),
                'role' => 'RegionalUser',
                'regionId' => 1,
                'areaId' => 1,
                'salesTypeId' => 2,
            ],
            [
                'userId' => 1,
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'passwordHash' => password_hash('cclpi', PASSWORD_DEFAULT),
                'role' => 'Admin',
                'regionId' => 1,
                'areaId' => 1,
                'salesTypeId' => 1,
            ],
            [
                'userId' => 2,
                'name' => 'System Admin',
                'email' => 'admin@cclpi.com',
                'passwordHash' => password_hash('cclpi', PASSWORD_DEFAULT),
                'role' => 'SystemAdmin',
                'regionId' => null,
                'areaId' => null,
                'salesTypeId' => null,
            ],
            [
                'userId' => 3,
                'name' => 'Viewer User',
                'email' => 'viewer@cclpi.com',
                'passwordHash' => password_hash('cclpi', PASSWORD_DEFAULT),
                'role' => 'Viewer',
                'regionId' => null,
                'areaId' => null,
                'salesTypeId' => null,
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
