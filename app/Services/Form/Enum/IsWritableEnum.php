<?php

namespace App\Services\Form\Enum;

abstract class IsWritableEnum
{
    public const STUDENT = 1;

    public const TEACHER = 2;

    public const TYPES = [
        self::STUDENT,
        self::TEACHER,
    ];
}
