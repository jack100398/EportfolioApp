<?php

namespace App\Policies;

use App\Models\Auth\Role;
use App\Models\Auth\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class RolePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): Response | bool
    {
        return true;
    }

    public function view(User $user, Role $role): Response | bool
    {
        return true;
    }

    public function create(User $user): Response | bool
    {
        return $user->hasRole([Role::SUPER_ADMIN, Role::ADMIN]);
    }

    public function update(User $user, Role $role): Response | bool
    {
        return true;
    }

    public function delete(User $user, Role $role): Response | bool
    {
        return true;
    }
}
