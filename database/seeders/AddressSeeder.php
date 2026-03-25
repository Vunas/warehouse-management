<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AddressSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('cities')->insert([
            ['id' => 1, 'name' => 'Hà Nội', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'name' => 'Hồ Chí Minh', 'created_at' => $now, 'updated_at' => $now],
        ]);

        DB::table('districts')->insert([
            ['id' => 1, 'city_id' => 1, 'name' => 'Quận Cầu Giấy', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'city_id' => 1, 'name' => 'Quận Đống Đa', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'city_id' => 2, 'name' => 'Quận 1', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'city_id' => 2, 'name' => 'Thành phố Thủ Đức', 'created_at' => $now, 'updated_at' => $now],
        ]);

        DB::table('wards')->insert([
            ['id' => 1, 'district_id' => 1, 'name' => 'Phường Dịch Vọng', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'district_id' => 3, 'name' => 'Phường Bến Nghé', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}
