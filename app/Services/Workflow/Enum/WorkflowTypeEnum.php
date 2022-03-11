<?php

namespace App\Services\Workflow\Enum;

/**
 * 簽核分類.
 */
abstract class WorkflowTypeEnum
{
    /**
     * 門檻表單.
     */
    public const THRESHOLD = 1;

    /**
     * 課程表單.
     */
    public const COURSE = 2;

    /**
     * 人工發送表單.
     */
    public const MANUAL = 3;

    public const TYPES = [
        self::THRESHOLD,
        self::COURSE,
        self::MANUAL,
    ];
}
