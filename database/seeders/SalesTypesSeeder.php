<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SalesTypesSeeder extends Seeder
{
    public function run()
    {
        DB::table('sales_types')->insert([
            ['salesTypeName' => 'Traditional'],
            ['salesTypeName' => 'Hybrid'],
        ]);
    }
}
