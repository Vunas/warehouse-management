<?php

namespace App\Services;

use App\Repositories\Interfaces\SupplierRepositoryInterface;

class SupplierService
{
    protected $supplierRepo;

    public function __construct(SupplierRepositoryInterface $supplierRepo)
    {
        $this->supplierRepo = $supplierRepo;
    }

    public function getPaginatedSuppliers($perPage = 15)
    {
        return $this->supplierRepo->paginate($perPage);
    }

    public function getSupplierById($id)
    {
        return $this->supplierRepo->findById($id, ['*'], ['products']);
    }

    public function createSupplier(array $data)
    {
        return $this->supplierRepo->create($data);
    }

    public function updateSupplier($id, array $data)
    {
        return $this->supplierRepo->update($id, $data);
    }

    public function deleteSupplier($id)
    {
        return $this->supplierRepo->softDelete($id);
    }
}