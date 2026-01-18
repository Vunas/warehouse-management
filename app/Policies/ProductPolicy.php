<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Product;

class ProductPolicy
{
    public function viewAny(User $user)
    {
        // Mọi nhân viên đều cần xem sản phẩm
        return $user->employee !== null;
    }

    public function view(User $user, Product $product)
    {
        return $this->viewAny($user);
    }

    public function create(User $user)
    {
        // Ai được tạo Hợp đồng/Nhập kho thường được tạo sản phẩm
        return $user->employee && ($user->employee->hasPermission('contract.create') || $user->employee->hasPermission('inbound.create'));
    }

    public function update(User $user, Product $product)
    {
        return $this->create($user);
    }

    public function delete(User $user, Product $product)
    {
        return $this->create($user);
    }
}