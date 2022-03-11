<?php

namespace App\Models\Exam\QuestionMetadata;

use App\Models\Exam\ExamQuestion;

class OptionFactory
{
    public static function make(int $type, array $metadata): IOption
    {
        $questionTypes = [
            ExamQuestion::TYPE_TRUEFALSE => TrueFalseOption::class,
            ExamQuestion::TYPE_CHOICE => ChoiceOption::class,
            ExamQuestion::TYPE_FILL => FillOption::class,
            ExamQuestion::TYPE_ESSAY => EssayOption::class,
        ];

        return new $questionTypes[$type]($metadata);
    }
}
