<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RegionSeeder extends Seeder
{
    public function run(): void
    {
        $province = DB::table('regions')->insertGetId([
            'region_type' => 'province',
            'code' => '11',
            'name' => 'Jawa Barat',
            'parent_id' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $regency = DB::table('regions')->insertGetId([
            'region_type' => 'regency',
            'code' => '1101',
            'name' => 'Bandung',
            'parent_id' => $province,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $district = DB::table('regions')->insertGetId([
            'region_type' => 'district',
            'code' => '110101',
            'name' => 'Cibeunying',
            'parent_id' => $regency,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        echo "Region IDs: province={$province}, regency={$regency}, district={$district}\n";
    }
}
