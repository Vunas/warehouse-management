<?php

namespace App\Repositories;

use App\Models\Contract;
use App\Models\ContractBlock;

class ContractRepository
{
    public function getAllPaginated($perPage = 10)
    {
        return Contract::with(['customer.user', 'contractBlocks'])->latest()->paginate($perPage);
    }

    public function findById($id)
    {
        return Contract::with(['customer', 'contractBlocks.storageBlock.warehouse'])->findOrFail($id);
    }

    public function create(array $data)
    {
        return Contract::create($data);
    }

    public function update($id, array $data)
    {
        $contract = $this->findById($id);
        $contract->update($data);
        return $contract;
    }

    public function createBlockRent(array $data)
    {
        return ContractBlock::create($data);
    }
    
    public function getActiveContracts()
    {
        return Contract::where('status', 'active')->get();
    }
}