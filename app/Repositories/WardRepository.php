<?php

namespace App\Repositories;

use App\Models\Ward;
use App\Repositories\Interfaces\WardRepositoryInterface;
use App\Repositories\Traits\CanRead;
use App\Repositories\Traits\CanWrite;
use App\Repositories\Traits\CanDelete;

class WardRepository extends BaseRepository implements WardRepositoryInterface
{
    use CanRead, CanWrite, CanDelete;

    public function getModel()
    {
        return Ward::class;
    }

    // Nghiệp vụ cụ thể: Lấy Phường/Xã theo Quận/Huyện
    public function getByDistrictId(int $districtId)
    {
        return $this->model->where('district_id', $districtId)->get();
    }
}