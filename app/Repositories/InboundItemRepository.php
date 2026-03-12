<?php

namespace App\Repositories;

use App\Models\InboundItem;
use App\Repositories\Interfaces\InboundItemRepositoryInterface;
use App\Repositories\Traits\CanRead;
use App\Repositories\Traits\CanWrite;
use App\Repositories\Traits\CanDelete;

class InboundItemRepository extends BaseRepository implements InboundItemRepositoryInterface
{
    use CanRead;
    use CanWrite {
        create as traitCreate;
    }
    use CanDelete;

    public function getModel()
    {
        return InboundItem::class;
    }

    public function create(array $payload)
    {
        return $this->traitCreate($payload);
    }

    public function getByInboundId(int $inboundId)
    {
        return $this->model->with('product')->where('inbound_id', $inboundId)->get();
    }
}