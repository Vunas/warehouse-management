<?php

namespace App\Services;

use App\Repositories\Interfaces\ContractRepositoryInterface;
use App\Repositories\Interfaces\WarehouseRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Exception;

class ContractService
{
    protected $contractRepo;
    protected $warehouseRepo;

    public function __construct(
        ContractRepositoryInterface $contractRepo,
        WarehouseRepositoryInterface $warehouseRepo
    ) {
        $this->contractRepo = $contractRepo;
        $this->warehouseRepo = $warehouseRepo;
    }

    public function getContractsPaginated()
    {
        return $this->contractRepo->paginate();
    }

      public function getContractById($id)
    {
        return $this->contractRepo->findById($id);
    }


    public function getContractsByCustomerID($CustomerID){
        return $this->contractRepo->getByCustomer($CustomerID);
    }

    public function getActiveContracts()
    {
        return $this->contractRepo->getActiveContracts();
    }

    public function createContract(array $data)
    {
        DB::beginTransaction();

        try {
            $contract = $this->contractRepo->create([
                'customer_id' => $data['customer_id'],
                'contract_code' => $data['contract_code'], // FIX
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'penalty_markup' => $data['penalty_markup'] ?? 0,
                'status' => 'ACTIVE',
            ]);

            foreach ($data['blocks'] as $blockData) {

                $block = $this->warehouseRepo->findBlockForUpdate($blockData['id']);

                if (strtoupper($block->status) !== 'AVAILABLE') {
                    throw new Exception("Lô {$block->block_code} không khả dụng.");
                }

                $this->contractRepo->createBlockRent([
                    'contract_id' => $contract->id,
                    'block_id' => $block->id,
                    'slots_committed' => $block->total_slots, 
                    'rented_from' => $data['start_date'],
                    'rented_to' => $data['end_date'],
                    'rental_price' => $blockData['price'],
                ]);

                $this->warehouseRepo->updateBlock(
                    $block->id,
                    ['status' => 'RENTED']
                );
            }

            DB::commit();
            return $contract;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }


    public function terminateContract($id)
    {
        DB::beginTransaction();
        try {
            $contract = $this->contractRepo->findById($id);

            foreach ($contract->contractBlocks as $cb) {
                $this->warehouseRepo->updateBlock($cb->block_id, ['status' => 'AVAILABLE']);
            }

            $this->contractRepo->update($id, ['status' => 'EXPIRED']);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function countActive()
    {
        return $this->contractRepo->countByStatus('active');
        
    }
    public function updateContract($id, array $data)
    {
        return $this->contractRepo->update($id, $data);
    }
    public function deleteContract($id)
    {
        DB::beginTransaction();
        try {
            $contract = $this->contractRepo->findById($id);

            // Logic bảo vệ: Không cho xóa hợp đồng đã có phát sinh nhập/xuất kho
            if ($contract->inboundTickets()->exists() || $contract->outboundTickets()->exists()) {
                throw new Exception("Không thể xóa hợp đồng đã có giao dịch nhập/xuất kho. Hãy sử dụng chức năng Thanh lý/Hủy.");
            }

            if ($contract->status === 'ACTIVE') {
                foreach ($contract->contractBlocks as $cb) {
                    $this->warehouseRepo->updateBlock($cb->block_id, ['status' => 'AVAILABLE']);
                }
            }

            $this->contractRepo->delete($id);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
