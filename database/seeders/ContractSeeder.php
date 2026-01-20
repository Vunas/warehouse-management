<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Contract;
use App\Models\Customer;
use Carbon\Carbon;

class ContractSeeder extends Seeder
{
    public function run(): void
    {
        $custA = Customer::where('company_name', 'Construction Co. Ltd')->first();

        $blockA1 = DB::table('storage_blocks')->where('block_code', 'A-01')->first();

        if ($custA) {
            $contract = Contract::updateOrCreate(
                ['contract_code' => 'HD-2024-001'],
                [
                    'customer_id' => $custA->id,
                    'start_date' => Carbon::now()->subDays(30),
                    'end_date' => Carbon::now()->addYear(),
                    'penalty_markup' => 1.5,
                    'status' => 'Active',
                ]
            );

            if ($blockA1) {
                $exists = DB::table('contract_blocks')
                    ->where('contract_id', $contract->id)
                    ->where('block_id', $blockA1->id)
                    ->exists();

                if (!$exists) {
                    DB::table('contract_blocks')->insert([
                        'contract_id' => $contract->id,
                        'block_id' => $blockA1->id,
                        'slots_committed' => 100,
                        'rented_from' => Carbon::now()->subDays(30),
                        'rented_to' => Carbon::now()->addYear(),
                        'rental_price' => 5000000,
                        'created_at' => now(),
                    ]);

                    DB::table('storage_blocks')
                        ->where('id', $blockA1->id)
                        ->update(['status' => 'rented']);
                }
            }
        }

        Contract::factory()->count(10)->create();

        echo "Seeded: 1 Fixed Contract & 10 Random Contracts.\n";
    }
}
