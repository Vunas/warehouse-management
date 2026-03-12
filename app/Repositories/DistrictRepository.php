<?php

namespace App\Repositories;

use App\Models\District;
use App\Repositories\Interfaces\DistrictRepositoryInterface;
use App\Repositories\Traits\CanRead;
use App\Repositories\Traits\CanWrite;
use App\Repositories\Traits\CanDelete;

class DistrictRepository extends BaseRepository implements DistrictRepositoryInterface
{
    use CanRead, CanWrite, CanDelete;

    public function getModel()
    {
        return District::class;
    }

    // Nghiệp vụ cụ thể: Lấy Quận/Huyện theo Tỉnh/Thành
    public function getByCityId(int $cityId)
    {
        return $this->model->where('city_id', $cityId)->get();
    }
}