<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Region;

class RegionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $regions = [
            [
                'regionId' => 1,
                'regionName' => 'NCR',
                'areaId' => 1,
            ],
            [
                'regionId' => 2,
                'regionName' => 'CALABARSON',
                'areaId' => 1,
            ],
            [
                'regionId' => 3,
                'regionName' => 'GUMACA',
                'areaId' => 1,
            ],
            [
                'regionId' => 4,
                'regionName' => 'CALAPAN',
                'areaId' => 1,
            ],
            [
                'regionId' => 5,
                'regionName' => 'REGION 6',
                'areaId' => 2,
            ],
            [
                'regionId' => 6,
                'regionName' => 'REGION 7-8',
                'areaId' => 2,
            ],
            [
                'regionId' => 7,
                'regionName' => 'REGION 9',
                'areaId' => 3,
            ],
            [
                'regionId' => 8,
                'regionName' => 'REGION 10',
                'areaId' => 3,
            ],
            [
                'regionId' => 9,
                'regionName' => 'REGION 11',
                'areaId' => 3,
            ],
            [
                'regionId' => 10,
                'regionName' => 'REGION 12',
                'areaId' => 3,
            ],
            [
                'regionId' => 11,
                'regionName' => 'REGION 13',
                'areaId' => 3,
            ],
        ];

        foreach ($regions as $region) {
            Region::create($region);
        }
    }
}
