<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Brand;

class BrandPolicy
{
    public function viewAny(User $user): bool { return $user->hasPermissionTo('manage_brands'); }
    public function view(User $user, Brand $brand): bool { return $user->hasPermissionTo('manage_brands'); }
    public function create(User $user): bool { return $user->hasPermissionTo('manage_brands'); }
    public function update(User $user, Brand $brand): bool { return $user->hasPermissionTo('manage_brands'); }
    public function delete(User $user, Brand $brand): bool { return $user->hasPermissionTo('manage_brands'); }
}