<?php

namespace App\Services\Form\Enum;

/**
 * 表單共用種類.
 */
abstract class IsSharableEnum
{
    /**
     * 非共用.
     */
    public const NONE = 0;

    /**
     * 所有單位共用.
     */
    public const ALL = 1;

    /**
     * 單位內共用.
     */
    public const UNIT = 2;

    public const TYPES = [
        self::NONE,
        self::ALL,
        self::UNIT,
    ];
}
