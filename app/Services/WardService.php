<?php

namespace App\Services;

use App\Repositories\Interfaces\WardRepositoryInterface;

class WardService
{
    protected $wardRepo;

    public function __construct(WardRepositoryInterface $wardRepo)
    {
        $this->wardRepo = $wardRepo;
    }

    public function getWardsByDistrict($districtId)
    {
        return $this->wardRepo->getByDistrictId($districtId);
    }

    public function createWard(array $data)
    {
        return $this->wardRepo->create($data);
    }

    public function updateWard($id, array $data)
    {
        return $this->wardRepo->update($id, $data);
    }

    public function deleteWard($id)
    {
        return $this->wardRepo->delete($id);
    }
}