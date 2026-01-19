<?php

namespace App\Repositories\Interfaces;

interface WarehouseRepositoryInterface
{
    public function getAvailableBlocks();
    public function getAllWithRelations();
    public function paginate($perPage = 10);
    public function findById($id);
    public function create($data);
    public function update($id, $data);
    public function delete($id);
    public function getSelectable();
    public function createBlock($data);
    public function findBlockById($id);
    public function updateBlock($id, $data);
    public function sumTotalCapacity();
    public function getRentableWarehousesWithAvailableBlocks();
    public function findBlockForUpdate($id);

}
