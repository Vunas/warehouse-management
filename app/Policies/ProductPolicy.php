<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Product;

class ProductPolicy
{
    public function viewAny(User $user)
    {
        return $user->employee !== null;
    }

    public function view(User $user, Product $product)
    {
        return $this->viewAny($user);
    }

    public function create(User $user)
    {
        return $user->employee && $user->employee->hasPermission('product.create');
    }

    public function update(User $user, Product $product)
    {
        return $user->employee && $user->employee->hasPermission('product.update');
    }

    public function delete(User $user, Product $product)
    {
        return $user->employee && $user->employee->hasPermission('product.delete');
    }
}