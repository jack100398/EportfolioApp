<?php

namespace App\Services\Workflow\Enum;

/**
 * 簽核種類.
 */
abstract class ProcessTypeEnum
{
    /**
     * 簽核.
     */
    public const SINGLE = 1;

    /**
     * 通知.
     */
    public const NOTIFY = 2;

    /**
     * 填寫.
     */
    public const FILL = 3;

    /**
     * 受評者.
     */
    public const EVALUATEE = 4;

    /**
     * 匿名填寫.
     */
    public const ANONYMOUS = 5;

    public const TYPES = [
        self::SINGLE,
        self::NOTIFY,
        self::FILL,
        self::EVALUATEE,
        self::ANONYMOUS,
    ];
}
