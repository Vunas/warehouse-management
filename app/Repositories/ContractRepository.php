<?php

namespace App\Repositories;

use App\Models\Contract;
use App\Models\ContractBlock;
use App\Repositories\Interfaces\ContractRepositoryInterface;

class ContractRepository implements ContractRepositoryInterface
{
    protected $model;

    public function __construct(Contract $model)
    {
        $this->model = $model;
    }

    public function paginate($perPage = 10)
    {
        return $this->model->with(['customer', 'contractBlocks'])->latest()->paginate($perPage);
    }

    public function findById($id)
    {
        return $this->model->with(['customer', 'contractBlocks.storageBlock.warehouse'])->findOrFail($id);
    }

    public function create($data)
    {
        return $this->model->create($data);
    }

    public function update($id, $data)
    {
        $contract = $this->findById($id);
        $contract->update($data);
        return $contract;
    }

    public function delete($id)
    {
        return $this->model->destroy($id);
    }

    public function getSelectable()
    {
        return $this->model->where('status', 'ACTIVE')
            ->select('id', 'contract_number as name')
            ->get();
    }

    public function createBlockRent($data)
    {
        return ContractBlock::create($data);
    }

    public function getActiveContracts()
    {
        return $this->model->where('status', 'ACTIVE')->get();
    }

    public function getByCustomer($customerId)
    {
        return $this->model->where('customer_id', $customerId)->get();
    }

    public function countByStatus($status)
    {
        return Contract::where('status', $status)->count();
    }
}
