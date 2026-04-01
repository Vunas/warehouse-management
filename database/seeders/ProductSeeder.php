<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // 1. Categories (Danh mục phụ tùng)
        DB::table('categories')->insert([
            ['id' => 1, 'name' => 'Động cơ', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'name' => 'Hệ thống phanh', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'name' => 'Hệ thống điện', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'name' => 'Lọc & dầu nhớt', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 5, 'name' => 'Hệ thống treo', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 6, 'name' => 'Hệ thống làm mát', 'created_at' => $now, 'updated_at' => $now],
        ]);

        // 2. Brands (Hãng phụ tùng)
        DB::table('brands')->insert([
            ['id' => 1, 'name' => 'Bosch', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'name' => 'Denso', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'name' => 'Toyota Genuine Parts', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'name' => 'NGK', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 5, 'name' => 'Mobil', 'created_at' => $now, 'updated_at' => $now],
        ]);

        // 3. Products (Sản phẩm phụ tùng)
        DB::table('products')->insert([
            // ===== BOSCH (1) =====
            ['category_id'=>1,'brand_id'=>1,'name'=>'Bugi Bosch Platinum','description'=>'Đánh lửa ổn định','price'=>150000,'is_active'=>1,'created_at'=>$now,'updated_at'=>$now],
            ['category_id'=>4,'brand_id'=>1,'name'=>'Lọc dầu Bosch','description'=>'Lọc sạch cặn bẩn','price'=>120000,'is_active'=>1,'created_at'=>$now,'updated_at'=>$now],
            ['category_id'=>6,'brand_id'=>1,'name'=>'Bơm nước Bosch','description'=>'Làm mát động cơ','price'=>950000,'is_active'=>1,'created_at'=>$now,'updated_at'=>$now],
            ['category_id'=>3,'brand_id'=>1,'name'=>'Cảm biến oxy Bosch','description'=>'Tối ưu nhiên liệu','price'=>700000,'is_active'=>1,'created_at'=>$now,'updated_at'=>$now],
            ['category_id'=>2,'brand_id'=>1,'name'=>'Đĩa phanh Bosch','description'=>'Phanh an toàn','price'=>1100000,'is_active'=>1,'created_at'=>$now,'updated_at'=>$now],

            // ===== DENSO (2) =====
            ['category_id'=>2,'brand_id'=>2,'name'=>'Má phanh Denso','description'=>'Bền bỉ','price'=>800000,'is_active'=>1,'created_at'=>$now,'updated_at'=>$now],
            ['category_id'=>3,'brand_id'=>2,'name'=>'Bugi Denso Iridium','description'=>'Tuổi thọ cao','price'=>220000,'is_active'=>1,'created_at'=>$now,'updated_at'=>$now],
            ['category_id'=>6,'brand_id'=>2,'name'=>'Két nước Denso','description'=>'Tản nhiệt nhanh','price'=>1500000,'is_active'=>1,'created_at'=>$now,'updated_at'=>$now],
            ['category_id'=>1,'brand_id'=>2,'name'=>'Kim phun nhiên liệu Denso','description'=>'Phun chính xác','price'=>1300000,'is_active'=>1,'created_at'=>$now,'updated_at'=>$now],
            ['category_id'=>3,'brand_id'=>2,'name'=>'Máy phát điện Denso','description'=>'Ổn định điện','price'=>2500000,'is_active'=>1,'created_at'=>$now,'updated_at'=>$now],

            // ===== TOYOTA (3) =====
            ['category_id'=>3,'brand_id'=>3,'name'=>'Ắc quy Toyota 12V','description'=>'Chính hãng','price'=>1800000,'is_active'=>1,'created_at'=>$now,'updated_at'=>$now],
            ['category_id'=>5,'brand_id'=>3,'name'=>'Giảm xóc Toyota','description'=>'Êm ái','price'=>2200000,'is_active'=>1,'created_at'=>$now,'updated_at'=>$now],
            ['category_id'=>4,'brand_id'=>3,'name'=>'Lọc gió Toyota','description'=>'Tăng hiệu suất','price'=>300000,'is_active'=>1,'created_at'=>$now,'updated_at'=>$now],
            ['category_id'=>6,'brand_id'=>3,'name'=>'Két nước Toyota','description'=>'Làm mát tốt','price'=>1700000,'is_active'=>1,'created_at'=>$now,'updated_at'=>$now],
            ['category_id'=>1,'brand_id'=>3,'name'=>'Dây curoa Toyota','description'=>'Truyền động ổn định','price'=>400000,'is_active'=>1,'created_at'=>$now,'updated_at'=>$now],

            // ===== NGK (4) =====
            ['category_id'=>1,'brand_id'=>4,'name'=>'Bugi NGK Iridium','description'=>'Hiệu suất cao','price'=>200000,'is_active'=>1,'created_at'=>$now,'updated_at'=>$now],
            ['category_id'=>3,'brand_id'=>4,'name'=>'Mobin đánh lửa NGK','description'=>'Đánh lửa mạnh','price'=>900000,'is_active'=>1,'created_at'=>$now,'updated_at'=>$now],
            ['category_id'=>4,'brand_id'=>4,'name'=>'Lọc gió NGK','description'=>'Lọc sạch không khí','price'=>250000,'is_active'=>1,'created_at'=>$now,'updated_at'=>$now],
            ['category_id'=>1,'brand_id'=>4,'name'=>'Dây bugi NGK','description'=>'Truyền điện tốt','price'=>350000,'is_active'=>1,'created_at'=>$now,'updated_at'=>$now],
            ['category_id'=>3,'brand_id'=>4,'name'=>'Cảm biến nhiệt NGK','description'=>'Đo nhiệt chính xác','price'=>500000,'is_active'=>1,'created_at'=>$now,'updated_at'=>$now],

            // ===== MOBIL (5) =====
            ['category_id'=>4,'brand_id'=>5,'name'=>'Dầu nhớt Mobil 1','description'=>'Bảo vệ động cơ','price'=>900000,'is_active'=>1,'created_at'=>$now,'updated_at'=>$now],
            ['category_id'=>4,'brand_id'=>5,'name'=>'Dầu hộp số Mobil','description'=>'Sang số mượt','price'=>750000,'is_active'=>1,'created_at'=>$now,'updated_at'=>$now],
            ['category_id'=>4,'brand_id'=>5,'name'=>'Dầu phanh Mobil','description'=>'Ổn định áp suất','price'=>200000,'is_active'=>1,'created_at'=>$now,'updated_at'=>$now],
            ['category_id'=>6,'brand_id'=>5,'name'=>'Dung dịch làm mát Mobil','description'=>'Chống quá nhiệt','price'=>300000,'is_active'=>1,'created_at'=>$now,'updated_at'=>$now],
            ['category_id'=>1,'brand_id'=>5,'name'=>'Phụ gia động cơ Mobil','description'=>'Tăng hiệu suất','price'=>250000,'is_active'=>1,'created_at'=>$now,'updated_at'=>$now],
        ]);
    }
}
