<?php

namespace App\Services;

abstract class UnitUserEnum
{
    /**
     * 所屬單位.
     */
    public const DEFAULT = 0;

    /**
     * 訓練單位.
     */
    public const TRAINING = 2;

    public const TYPES = [
        self::DEFAULT,
        self::TRAINING,
    ];
}
