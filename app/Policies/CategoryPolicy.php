<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Category;

class CategoryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view_categories');
    }

    public function view(User $user, Category $category): bool
    {
        return $user->hasPermissionTo('view_categories');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create_categories');
    }

    public function update(User $user, Category $category): bool
    {
        return $user->hasPermissionTo('edit_categories');
    }

    public function delete(User $user, Category $category): bool
    {
        return $user->hasPermissionTo('delete_categories');
    }
}