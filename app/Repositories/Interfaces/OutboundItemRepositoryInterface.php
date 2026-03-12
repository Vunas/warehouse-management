<?php

namespace App\Repositories\Interfaces;

interface OutboundItemRepositoryInterface
{
    public function findById($id, array $columns = ['*'], array $relations = []);
    public function getByOutboundId(int $outboundId);
    
    public function create(array $payload);
    public function delete($id);
}