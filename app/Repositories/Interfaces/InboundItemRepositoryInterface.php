<?php

namespace App\Repositories\Interfaces;

interface InboundItemRepositoryInterface
{
    public function findById($id, array $columns = ['*'], array $relations = []);
    public function getByInboundId(int $inboundId);
    public function findByInboundAndProduct(int $inboundId, int $productId);

    public function create(array $payload);
    public function delete($id);
    public function update($id, array $data);
    // KHÔNG CÓ update(). Sai thì xóa dòng đó nhập lại dòng mới
}
