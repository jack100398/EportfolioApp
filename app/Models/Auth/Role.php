<?php

namespace App\Models\Auth;

use Spatie\Permission\Models\Role as RoleModel;

class Role extends RoleModel
{
    public const STUDENT = 'student';

    public const TEACHER = 'teacher';

    public const UNIT_MANAGER = 'unitManager';

    public const ADMIN = 'admin';

    public const SUPER_ADMIN = 'superAdmin';
}
