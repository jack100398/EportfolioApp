<?php

namespace App\Services\Form\Enum;

abstract class ReviewedEnum
{
    /**
     * 未審核.
     */
    public const UNAPPROVED = 0;

    /**
     * 審核通過.
     */
    public const PASS = 1;

    /**
     * 審核拒絕.
     */
    public const REFUSE = 2;

    /**
     * 編輯中，先不要讓單位管理者審核.
     */
    public const EDIT = 3;

    public const TYPES = [
        self::UNAPPROVED,
        self::PASS,
        self::REFUSE,
        self::EDIT,
    ];
}
