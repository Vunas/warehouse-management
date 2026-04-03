<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Product;

class ProductBatchPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view_product_batches');
    }

    public function view(User $user, Product $product): bool
    {
        return $user->hasPermissionTo('view_product_batches');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create_product_batches');
    }

    public function update(User $user, Product $product): bool
    {
        return $user->hasPermissionTo('edit_product_batches');
    }

    public function delete(User $user, Product $product): bool
    {
        return $user->hasPermissionTo('delete_product_batches');
    }
}