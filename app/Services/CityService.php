<?php

namespace App\Services;

use App\Repositories\Interfaces\CityRepositoryInterface;

class CityService
{
    protected $cityRepo;

    public function __construct(CityRepositoryInterface $cityRepo)
    {
        $this->cityRepo = $cityRepo;
    }

    public function getAllCities()
    {
        return $this->cityRepo->all();
    }

    public function getCityById($id)
    {
        return $this->cityRepo->findById($id);
    }

    public function createCity(array $data)
    {
        return $this->cityRepo->create($data);
    }

    public function updateCity($id, array $data)
    {
        return $this->cityRepo->update($id, $data);
    }

    public function deleteCity($id)
    {
        return $this->cityRepo->delete($id);
    }
}