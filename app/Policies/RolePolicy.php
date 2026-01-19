<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Role;

class RolePolicy
{
    public function viewAny(User $user)
    {
        return $user->employee && $user->employee->hasPermission('role.view');
    }

    public function view(User $user, Role $role)
    {
        return $this->viewAny($user);
    }

    public function create(User $user)
    {
        return $user->employee && $user->employee->hasPermission('role.create');
    }

    public function update(User $user, Role $role)
    {
      
        if (in_array($role->name, ['Admin', 'Manager', 'Staff'])) {
            return false; 
        }
        return $user->employee && $user->employee->hasPermission('role.update');
    }

    public function delete(User $user, Role $role)
    { 
        if (in_array($role->name, ['Admin', 'Manager', 'Staff'])) {
            return false;
        }
        return $user->employee && $user->employee->hasPermission('role.delete');
    }
}