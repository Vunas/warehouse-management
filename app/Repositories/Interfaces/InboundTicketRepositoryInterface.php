<?php

namespace App\Repositories\Interfaces;

interface InboundTicketRepositoryInterface
{
    public function paginate($perPage = 15);
    public function findById($id);
    public function create($data);
    public function update($id, $data);
    public function delete($id);
    public function createDetail($data);
    public function createCalculatedSlot($data);
    public function updateStatus($id, $status);
    public function countByStatus($status);
    public function getLatest($limit = 5);
}
