<?php

namespace App\Models\Auth;

class AuthorizationAbility
{
    public const INDEX = 'viewAny';

    public const SHOW = 'view';

    public const STORE = 'create';

    public const UPDATE = 'update';

    public const DESTROY = 'delete';
}
