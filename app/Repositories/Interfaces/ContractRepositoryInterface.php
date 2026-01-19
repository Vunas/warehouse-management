<?php

namespace App\Repositories\Interfaces;

interface ContractRepositoryInterface
{
    public function paginate($perPage = 10);
    public function findById($id);
    public function create($data);
    public function update($id, $data);
    public function delete($id);
    public function getSelectable();
    public function createBlockRent($data);
    public function getActiveContracts();
    public function getByCustomer($customerId);
    public function countByStatus($status);
}
