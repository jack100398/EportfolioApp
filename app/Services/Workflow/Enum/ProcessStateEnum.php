<?php

namespace App\Services\Workflow\Enum;

abstract class ProcessStateEnum
{
    /**
     * 未開始.
     */
    public const NO_START = 0;

    /**
     * 開始.
     */
    public const STARTED = 1;

    /**
     * 同意.
     */
    public const AGREE = 2;

    /**
     * 不同意(退件).
     */
    public const DISAGREE = 3;

    /**
     * 加簽.
     */
    public const FORWARDED = 4;

    /**
     * 退回上一層.
     */
    public const RETURN = 5;

    /**
     * 單純結束(適用於填寫或通知).
     */
    public const END = 6;

    /**
     * 退回(某一層).
     */
    public const BACK = 7;
}
