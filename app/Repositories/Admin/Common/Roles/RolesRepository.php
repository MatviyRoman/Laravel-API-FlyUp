<?php

namespace App\Repositories\Admin\Common\Roles;

use App\Models\Role;

class RolesRepository
{
    public function getRolesByNames(array $roles)
    {
        return Role::whereIn('role_name', $roles)->get();
    }

    public function getRoleByName($role)
    {
        return Role::where('role_name', $role)->first();
    }
}
