<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AreasSeeder::class,
            RegionsSeeder::class,
            // SalesTypesSeeder::class, // Data is inserted in migration
            UsersSeeder::class,
            SalesTargetsSeeder::class,
            SalesSeeder::class,
        ]);
    }
}
