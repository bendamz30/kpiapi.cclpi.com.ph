<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AreasSeeder extends Seeder
{
    public function run()
    {
        $areas = [
            ['areaId' => 1, 'areaName' => 'Luzon'],
            ['areaId' => 2, 'areaName' => 'Visayas'],
            ['areaId' => 3, 'areaName' => 'Mindanao'],
        ];

        DB::table('areas')->insert($areas);
    }
}
