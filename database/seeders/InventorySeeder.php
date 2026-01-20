<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\InboundTicket;
use App\Models\InventoryItem;

class InventorySeeder extends Seeder
{
    public function run(): void
    {

        $contract = DB::table('contracts')->where('contract_code', 'HD-2024-001')->first();
        $product = DB::table('products')->where('sku', 'STL-001')->first();
        $block = DB::table('storage_blocks')->where('block_code', 'A-01')->first();
        $rule = DB::table('size_conversion_rules')->where('rule_name', 'like', '%Big%')->first();

        if ($contract && $product && $block && $rule) {

            $inboundId = DB::table('inbound_tickets')->insertGetId([
                'contract_id' => $contract->id,
                'expected_date' => now()->subDays(5),
                'status' => 'Approved',
                'created_at' => now()->subDays(5),
            ]);

            $detailId = DB::table('inbound_details')->insertGetId([
                'inbound_id' => $inboundId,
                'product_id' => $product->id,
                'input_length' => 2.5,
                'input_width' => 2.5,
                'input_height' => 2.0,
                'quantity' => 5,
                'created_at' => now()->subDays(5),
            ]);

            $calcId = DB::table('calculated_slots')->insertGetId([
                'inbound_detail_id' => $detailId,
                'rule_id' => $rule->id,
                'final_length' => 2.5,
                'final_width' => 2.5,
                'final_height' => 2.0,
                'final_slot_cost' => 6,
                'is_violation' => false,
                'created_at' => now()->subDays(5),
            ]);

            for ($i = 1; $i <= 5; $i++) {
                InventoryItem::create([
                    'block_id' => $block->id,
                    'product_id' => $product->id,
                    'calc_id' => $calcId,
                    'slot_used' => 6,
                    'imported_at' => now()->subDays(4),
                    'current_quantity' => 1,
                ]);
            }
        }

        InventoryItem::factory()->count(50)->create();

        InboundTicket::factory()->count(10)->create(['status' => 'Pending']);

        echo "Seeded: Inventory (Fixed & Random) and Pending Inbounds.\n";
    }
}
