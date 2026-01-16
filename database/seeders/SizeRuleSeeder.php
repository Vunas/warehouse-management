<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SizeRuleSeeder extends Seeder
{
    public function run(): void
    {
        $rules = [
            [
                'rule_name' => 'Cỡ Lớn (Big)',
                'max_length' => 3.0, 'max_width' => 3.0, 'max_height' => 3.0,
                'slot_cost' => 6,
                'priority_level' => 2,
                'is_active' => true,
                'created_at' => now()
            ],
            [
                'rule_name' => 'Cỡ Vừa (Medium)',
                'max_length' => 2.0, 'max_width' => 2.0, 'max_height' => 2.0,
                'slot_cost' => 3,
                'priority_level' => 1,
                'is_active' => true,
                'created_at' => now()
            ],
            [
                'rule_name' => 'Cỡ Nhỏ (Small)',
                'max_length' => 1.0, 'max_width' => 1.0, 'max_height' => 1.0,
                'slot_cost' => 1,
                'priority_level' => 0,
                'is_active' => true,
                'created_at' => now()
            ],
        ];

        foreach ($rules as $rule) {
            DB::table('size_conversion_rules')->updateOrInsert(['rule_name' => $rule['rule_name']], $rule);
        }
    }
}