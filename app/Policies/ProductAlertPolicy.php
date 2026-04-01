<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ProductAlert;

class ProductAlertPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view_alerts');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create_alerts');
    }

    public function update(User $user, ProductAlert $alert): bool
    {
        return $user->hasPermissionTo('edit_alerts');
    }

    public function delete(User $user, ProductAlert $alert): bool
    {
        return $user->hasPermissionTo('delete_alerts');
    }
}