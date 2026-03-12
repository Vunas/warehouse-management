<?php

namespace App\Repositories;

use App\Models\Address;
use App\Repositories\Interfaces\AddressRepositoryInterface;
use App\Repositories\Traits\CanRead;
use App\Repositories\Traits\CanWrite;
use App\Repositories\Traits\CanDelete;

class AddressRepository extends BaseRepository implements AddressRepositoryInterface
{
    use CanRead, CanWrite, CanDelete;

    public function getModel()
    {
        return Address::class;
    }

    // Nghiệp vụ cụ thể: Lấy danh sách địa chỉ của 1 User
    public function getByUserId(int $userId)
    {
        return $this->model->with(['ward.district.city'])->where('user_id', $userId)->get();
    }
}