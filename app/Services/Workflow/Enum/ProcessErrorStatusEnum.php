<?php

namespace App\Services\Workflow\Enum;

/**
 * 表單異常狀態.
 */
abstract class ProcessErrorStatusEnum
{
    /**
     * 正常.
     */
    public const NORMAL = 0;

    /**
     * 沒有設定簽核流程角色.
     */
    public const NO_SETTING_ROLE = 1;

    /**
     * 簽核流程人員已不存在(被刪除或未啟動).
     */
    public const NOT_FOUND_EVALUATEE = 2;

    public const TYPES = [
        self::NO_SETTING_ROLE,
        self::NOT_FOUND_EVALUATEE,
    ];
}
