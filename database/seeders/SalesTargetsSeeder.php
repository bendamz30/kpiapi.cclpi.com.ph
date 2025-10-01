<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SalesTarget;

class SalesTargetsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $targets = [
            [
                'salesRepId' => 103,
                'year' => 2025,
                'premiumTarget' => 100000.00,
                'salesCounselorTarget' => 50,
                'policySoldTarget' => 100,
                'agencyCoopTarget' => 25,
                'createdBy' => 1,
            ],
            [
                'salesRepId' => 104,
                'year' => 2025,
                'premiumTarget' => 150000.00,
                'salesCounselorTarget' => 75,
                'policySoldTarget' => 150,
                'agencyCoopTarget' => 30,
                'createdBy' => 1,
            ],
            [
                'salesRepId' => 105,
                'year' => 2025,
                'premiumTarget' => 80000.00,
                'salesCounselorTarget' => 40,
                'policySoldTarget' => 80,
                'agencyCoopTarget' => 20,
                'createdBy' => 1,
            ],
            [
                'salesRepId' => 106,
                'year' => 2025,
                'premiumTarget' => 120000.00,
                'salesCounselorTarget' => 60,
                'policySoldTarget' => 120,
                'agencyCoopTarget' => 25,
                'createdBy' => 1,
            ],
        ];

        foreach ($targets as $target) {
            SalesTarget::create($target);
        }
    }
}
