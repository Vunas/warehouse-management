<?php

namespace App\Services;

use App\Repositories\Interfaces\DistrictRepositoryInterface;

class DistrictService
{
    protected $districtRepo;

    public function __construct(DistrictRepositoryInterface $districtRepo)
    {
        $this->districtRepo = $districtRepo;
    }

    public function getDistrictsByCity($cityId)
    {
        return $this->districtRepo->getByCityId($cityId);
    }

    public function createDistrict(array $data)
    {
        return $this->districtRepo->create($data);
    }

    public function updateDistrict($id, array $data)
    {
        return $this->districtRepo->update($id, $data);
    }

    public function deleteDistrict($id)
    {
        return $this->districtRepo->delete($id);
    }
}