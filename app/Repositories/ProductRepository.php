<?php

namespace App\Repositories;

use App\Models\Product;

class ProductRepository
{
    public function getAllPaginated($perPage = 20)
    {
        return Product::with('category')->latest()->paginate($perPage);
    }

    public function create(array $data)
    {
        return Product::create($data);
    }

    public function update($id, array $data)
    {
        $product = Product::findOrFail($id);
        $product->update($data);
        return $product;
    }

    public function delete($id)
    {
        return Product::destroy($id);
    }
}