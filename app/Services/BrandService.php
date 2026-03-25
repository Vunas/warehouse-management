<?php

namespace App\Services;

use App\Repositories\Interfaces\BrandRepositoryInterface;
use Exception;

class BrandService
{
    protected $brandRepo;

    public function __construct(BrandRepositoryInterface $brandRepo)
    {
        $this->brandRepo = $brandRepo;
    }

    public function getPaginatedBrands($perPage = 15, $search = '')
    {
        $query = app()->make(\App\Models\Brand::class)->query();

        if (!empty($search)) {
            $query->where('name', 'like', "%{$search}%");
        }

        return $query->orderBy('id', 'desc')->paginate($perPage)->withQueryString();
    }

    public function createBrand(array $data)
    {
        return $this->brandRepo->create($data);
    }

    public function updateBrand($id, array $data)
    {
        return $this->brandRepo->update($id, $data);
    }

    public function deleteBrand($id)
    {
        return $this->brandRepo->softDelete($id);
    }
}