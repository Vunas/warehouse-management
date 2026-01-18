<?php

namespace App\Services;

use App\Models\Contract;
use App\Models\ContractBlock;
use Illuminate\Support\Facades\DB;
use Exception;

class ContractService
{
    public function createContract(array $data)
    {
        DB::beginTransaction();
        try {
            $contract = Contract::create([
                'customer_id' => $data['customer_id'],
                'contract_code' => $data['contract_code'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'penalty_markup' => $data['penalty_markup'] ?? 0,
                'status' => 'active'
            ]);

            foreach ($data['blocks'] as $blockData) {
                ContractBlock::create([
                    'contract_id' => $contract->id,
                    'block_id' => $blockData['id'],
                    'slots_committed' => $blockData['slots_committed'],
                    'rented_from' => $data['start_date'],
                    'rented_to' => $data['end_date'],
                    'rental_price' => $blockData['price'],
                ]);

                // Có thể update status của Block thành 'rented' nếu thuê trọn gói
            }

            DB::commit();
            return $contract;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updateContract($id, array $data)
    {
        // Logic update...
        $contract = Contract::findOrFail($id);
        $contract->update($data);
        return $contract;
    }
}