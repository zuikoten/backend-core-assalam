<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RegionFullSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('regions')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // PROVINSI
        $this->command->info('Seeding provinces...');
        $provinces = DB::table('indonesia_provinces')->get();

        foreach ($provinces as $province) {
            DB::table('regions')->insert([
                'region_type' => 'province',
                'code'        => $province->code,
                'name'        => $province->name,
                'parent_id'   => null,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }

        // KABUPATEN/KOTA
        $this->command->info('Seeding regencies (cities)...');
        $cities = DB::table('indonesia_cities')->get();

        foreach ($cities as $city) {
            $province = DB::table('regions')
                ->where('region_type', 'province')
                ->where('code', $city->province_code)
                ->first();

            if (!$province) continue;

            DB::table('regions')->insert([
                'region_type' => 'regency',
                'code'        => $city->code,
                'name'        => $city->name,
                'parent_id'   => $province->id,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }

        // KECAMATAN
        $this->command->info('Seeding districts...');
        $districts = DB::table('indonesia_districts')->get();

        foreach ($districts as $district) {
            $regency = DB::table('regions')
                ->where('region_type', 'regency')
                ->where('code', $district->city_code)  // ← city_code bukan regency_code
                ->first();

            if (!$regency) continue;

            DB::table('regions')->insert([
                'region_type' => 'district',
                'code'        => $district->code,
                'name'        => $district->name,
                'parent_id'   => $regency->id,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }

        $this->command->info('✅ Selesai! Data wilayah Indonesia berhasil diimport.');
    }
}
