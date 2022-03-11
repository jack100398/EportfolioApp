<?php

namespace App\Models\Auth;

use Spatie\Permission\Models\Permission as PermissionModel;

class Permission extends PermissionModel
{
    public const ADD = 'add.';

    public const EDIT = 'edit.';

    public const DELETE = 'delete.';

    public const VIEW = 'view.';

    protected $hidden = ['guard_name'];
}
