<?php

namespace App\Services;

use App\Repositories\Interfaces\CategoryRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\DB;

class CategoryService
{
    protected $categoryRepo;

    public function __construct(CategoryRepositoryInterface $categoryRepo)
    {
        $this->categoryRepo = $categoryRepo;
    }

    public function getAllCategories()
    {
        return $this->categoryRepo->getAll();
    }

    public function createCategory(array $data)
    {
        return $this->categoryRepo->create($data);
    }

    public function updateCategory($id, array $data)
    {
        return $this->categoryRepo->update($id, $data);
    }

    public function deleteCategory($id)
    {
        // Logic kiểm tra: Nếu danh mục đã có sản phẩm thì không cho xóa
        $category = $this->categoryRepo->findById($id);
        
        if ($category->products()->exists()) {
            throw new Exception("Không thể xóa danh mục đã có sản phẩm.");
        }

        return $this->categoryRepo->delete($id);
    }
}