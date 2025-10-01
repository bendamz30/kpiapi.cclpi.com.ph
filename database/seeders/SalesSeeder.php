<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Sale;

class SalesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $salesData = [
            [
                'salesRepId' => 103,
                'reportDate' => '2025-09-01',
                'premiumActual' => 540.00,
                'salesCounselorActual' => 1,
                'policySoldActual' => 1,
                'agencyCoopActual' => 1,
                'createdBy' => 1,
            ],
            [
                'salesRepId' => 104,
                'reportDate' => '2025-09-01',
                'premiumActual' => 1080.00,
                'salesCounselorActual' => 2,
                'policySoldActual' => 1,
                'agencyCoopActual' => 1,
                'createdBy' => 1,
            ],
            [
                'salesRepId' => 106,
                'reportDate' => '2025-08-01',
                'premiumActual' => 540.00,
                'salesCounselorActual' => 1,
                'policySoldActual' => 0,
                'agencyCoopActual' => 0,
                'createdBy' => 1,
            ],
            [
                'salesRepId' => 104,
                'reportDate' => '2025-08-05',
                'premiumActual' => 24000.00,
                'salesCounselorActual' => 2,
                'policySoldActual' => 2,
                'agencyCoopActual' => 0,
                'createdBy' => 1,
            ],
            [
                'salesRepId' => 103,
                'reportDate' => '2025-08-14',
                'premiumActual' => 15000.00,
                'salesCounselorActual' => 10,
                'policySoldActual' => 10,
                'agencyCoopActual' => 0,
                'createdBy' => 1,
            ],
            [
                'salesRepId' => 103,
                'reportDate' => '2025-07-11',
                'premiumActual' => 12000.00,
                'salesCounselorActual' => 0,
                'policySoldActual' => 0,
                'agencyCoopActual' => 0,
                'createdBy' => 1,
            ],
            [
                'salesRepId' => 103,
                'reportDate' => '2025-06-14',
                'premiumActual' => 10000.00,
                'salesCounselorActual' => 1,
                'policySoldActual' => 1,
                'agencyCoopActual' => 1,
                'createdBy' => 1,
            ],
            [
                'salesRepId' => 103,
                'reportDate' => '2025-05-01',
                'premiumActual' => 5000.00,
                'salesCounselorActual' => 3,
                'policySoldActual' => 4,
                'agencyCoopActual' => 2,
                'createdBy' => 1,
            ],
            [
                'salesRepId' => 103,
                'reportDate' => '2025-09-05',
                'premiumActual' => 540.00,
                'salesCounselorActual' => 0,
                'policySoldActual' => 0,
                'agencyCoopActual' => 0,
                'createdBy' => 1,
            ],
            [
                'salesRepId' => 103,
                'reportDate' => '2025-09-08',
                'premiumActual' => 150000.00,
                'salesCounselorActual' => 0,
                'policySoldActual' => 0,
                'agencyCoopActual' => 0,
                'createdBy' => 1,
            ],
            [
                'salesRepId' => 105,
                'reportDate' => '2025-09-08',
                'premiumActual' => 540.00,
                'salesCounselorActual' => 0,
                'policySoldActual' => 0,
                'agencyCoopActual' => 0,
                'createdBy' => 1,
            ],
        ];

        foreach ($salesData as $sale) {
            Sale::create($sale);
        }
    }
}
