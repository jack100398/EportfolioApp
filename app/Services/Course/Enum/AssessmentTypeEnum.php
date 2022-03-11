<?php

namespace App\Services\Course\Enum;

/**
 * 表單種類.
 */
abstract class AssessmentTypeEnum
{
    public const WRITTEN_TEST = 5;

    public const CLINICAL_TECHNOLOGY = 6;

    public const DOPS = 9;

    public const MINI_CEX = 10;

    public const CBD = 11;

    public const ONLINE_TEST = 16;

    public const REFLECTION_REPORT = 13;

    public const CASE_DISCUSSION = 18;

    public const FILE_UPLOAD = 14;

    public const DESIGNATED_RECORD = 24;

    public const ATTEND = 15;

    public const MATERIAL = 20;

    public const OTHER_FORM = 23;

    public const DIRECTLY_PASS = 25;
}
