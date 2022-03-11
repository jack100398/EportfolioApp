<?php

namespace App\Services\Form\Enum;

/**
 * 項目分類  1.暫存資料 2.填寫結果.
 */
abstract class FormWriteRecordFlagEnum
{
    public const TEMP = 1;

    public const RESULT = 2;

    public const TYPES = [self::TEMP, self::RESULT];
}
