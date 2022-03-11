<?php

namespace App\Services\Form\Enum;

/**
 * 表單種類.
 */
abstract class FormTypeEnum
{
    /**
     * 一般表單.
     */
    public const GENERALLY = 1;

    /**
     * Mini-CEX.
     */
    public const MINICEX = 2;

    /**
     * EPA.
     */
    public const EPA = 3;

    /**
     * DOPS.
     */
    public const DOPS = 4;

    /**
     * CbD.
     */
    public const DBD = 5;

    /**
     * Milestone.
     */
    public const MILESTONE = 6;

    public const TYPES = [
        self::GENERALLY,
        self::MINICEX,
        self::EPA,
        self::DOPS,
        self::DBD,
        self::MILESTONE,
    ];
}
