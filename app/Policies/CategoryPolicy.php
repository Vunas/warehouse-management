<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Category;

class CategoryPolicy
{
    public function viewAny(User $user)
    {

        return $user->employee !== null;
    }

    public function view(User $user, Category $category)
    {
        return $this->viewAny($user);
    }

    public function create(User $user)
    {

        return $user->employee && $user->employee->hasPermission('product.create');
    }

    public function update(User $user, Category $category)
    {
        return $user->employee && $user->employee->hasPermission('product.update');
    }

    public function delete(User $user, Category $category)
    {
        return $user->employee && $user->employee->hasPermission('product.delete');
    }
}
