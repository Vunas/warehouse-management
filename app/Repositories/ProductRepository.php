<?php

namespace App\Repositories;

use App\Models\Product;
use App\Repositories\Interfaces\ProductRepositoryInterface;

class ProductRepository implements ProductRepositoryInterface
{
    protected $model;

    public function __construct(Product $model)
    {
        $this->model = $model;
    }

    public function paginate($perPage = 20)
    {
        return $this->model->with('category')->latest()->paginate($perPage);
    }

    public function findById($id)
    {
        return $this->model->with('category')->findOrFail($id);
    }

    public function create($data)
    {
        return $this->model->create($data);
    }

    public function update($id, $data)
    {
        $product = $this->findById($id);
        $product->update($data);
        return $product;
    }

    public function delete($id)
    {
        return $this->model->destroy($id);
    }

    public function getSelectable()
    {
        return $this->model->select('id', 'name', 'sku')->get();
    }

    public function findBySku($sku)
    {
        return $this->model->where('sku', $sku)->first();
    }
}