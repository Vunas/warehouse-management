<?php

namespace App\Services;

use App\Repositories\Interfaces\InboundItemRepositoryInterface;

class InboundItemService
{
    protected $itemRepo;

    public function __construct(InboundItemRepositoryInterface $itemRepo)
    {
        $this->itemRepo = $itemRepo;
    }

    public function getItemsByInboundOrder($inboundId)
    {
        return $this->itemRepo->getByInboundId($inboundId);
    }

    public function addItemToInboundOrder(array $data)
    {
        return $this->itemRepo->create($data);
    }

    public function removeItemFromInboundOrder($id)
    {
        // Tuân thủ Interface: Chỉ có xóa, không có sửa
        return $this->itemRepo->delete($id);
    }
}