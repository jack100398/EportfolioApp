<?php

namespace App\Policies;

use App\Models\Auth\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): Response | bool
    {
        return true;
    }

    public function info(User $user, User $model): Response | bool
    {
        return $user->id === $model->id;
    }

    public function view(User $user, User $model): Response | bool
    {
        return true;
    }

    public function create(User $user): Response | bool
    {
        return true;
    }

    public function update(User $user, User $model): Response | bool
    {
        return true;
    }

    public function delete(User $user, User $model): Response | bool
    {
        return true;
    }
}
