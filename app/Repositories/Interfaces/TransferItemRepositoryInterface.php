<?php

namespace App\Repositories\Interfaces;

interface TransferItemRepositoryInterface
{
    public function findById($id, array $columns = ['*'], array $relations = []);
    public function getByTransferId(int $transferId);
    
    public function create(array $payload);
    public function delete($id);
}