<?php

namespace App\Services;

use App\Repositories\Interfaces\LocationRepositoryInterface;
use Exception;

class LocationService
{
    protected $locationRepo;

    public function __construct(LocationRepositoryInterface $locationRepo)
    {
        $this->locationRepo = $locationRepo;
    }

    /**
     * Lấy danh sách location của kho và build thành cấu trúc Cây (Tree)
     */
    public function getWarehouseTree(int $warehouseId)
    {
        // Lấy toàn bộ location của kho này
        $locations = $this->locationRepo->getByWarehouse($warehouseId);
        
        // Gọi hàm đệ quy để build cây
        return $this->buildTree($locations);
    }

    /**
     * Hàm đệ quy để gom các node con vào node cha
     */
    private function buildTree($locations, $parentId = null)
    {
        $branch = [];

        foreach ($locations as $location) {
            if ($location->parent_id == $parentId) {
                // Đệ quy tìm con của node hiện tại
                $children = $this->buildTree($locations, $location->id);
                
                if ($children) {
                    $location->children_tree = $children; // Gắn mảng con vào thuộc tính ảo
                } else {
                    $location->children_tree = [];
                }

                $branch[] = $location;
            }
        }

        return $branch;
    }

    public function createLocation(array $data)
    {
        // Kiểm tra an toàn: Nếu chọn parent_id, đảm bảo parent đó tồn tại và thuộc cùng kho
        if (!empty($data['parent_id'])) {
            $parent = $this->locationRepo->findById($data['parent_id']);
            if ($parent->warehouse_id != $data['warehouse_id']) {
                throw new Exception("Vị trí cha không nằm trong cùng một kho!");
            }
            if ($parent->is_store) {
                throw new Exception("Vị trí cha được đánh dấu là nơi chứa hàng (is_store=true), không thể tạo thêm vị trí con bên trong nó.");
            }
        }

        return $this->locationRepo->create($data);
    }

    public function updateLocation(int $id, array $data)
    {
        // CHẶN LỖI INFINITE LOOP (Vòng lặp vô tận): Parent ID không được trùng với chính ID của nó
        if (isset($data['parent_id']) && $data['parent_id'] == $id) {
            throw new Exception("Lỗi Logic: Một vị trí không thể tự làm cha của chính nó.");
        }

        // CHẶN: Không cho phép chọn parent_id là con/cháu của chính mình (Nâng cao)
        if (isset($data['parent_id'])) {
            $this->checkCircularReference($id, $data['parent_id']);
        }

        return $this->locationRepo->update($id, $data);
    }

    public function deleteLocation(int $id)
    {
        // 1. Kiểm tra xem location này có location con nào không
        $children = $this->locationRepo->getChildren($id);
        if ($children->count() > 0) {
            throw new Exception("Không thể xóa: Vị trí này đang chứa " . $children->count() . " vị trí con bên trong. Vui lòng xóa vị trí con trước.");
        }

        // 2. Tùy chọn: Nếu bạn có truy cập InventoryRepo ở đây, hãy check xem còn hàng tồn kho không
        // if ($this->inventoryRepo->countByLocation($id) > 0) { ... }
        
        return $this->locationRepo->delete($id);
    }

    /**
     * Kiểm tra đệ quy ngược lên trên để chống vòng lặp (Circular Reference)
     */
    private function checkCircularReference($locationId, $newParentId)
    {
        $currentParent = $this->locationRepo->findById($newParentId);
        
        while ($currentParent != null && $currentParent->parent_id != null) {
            if ($currentParent->parent_id == $locationId) {
                throw new Exception("Lỗi Logic: Không thể chuyển vị trí này vào bên trong vị trí con/cháu của chính nó.");
            }
            $currentParent = $this->locationRepo->findById($currentParent->parent_id);
        }
    }
}