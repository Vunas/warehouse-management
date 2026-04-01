<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductAlert;

class ProductAlertSeeder extends Seeder
{
    public function run(): void
    {

        $products = Product::all();

        foreach ($products as $product) {
            ProductAlert::updateOrCreate(
                ['product_id' => $product->id],
                [
                    'stock_threshold' => rand(5, 20),
                    'expiry_threshold_days' => rand(30, 120),
                    'is_active' => true,
                    'last_stock_alert_at' => null,
                    'last_expiry_alert_at' => null,
                ]
            );
        }
    }
}
