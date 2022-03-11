<?php

namespace App\Services\Form\QuestionTypes;

class BaseQuestionType
{
    protected int $type = 0;

    protected array $attributes = [];

    protected function transferBaseQuestionType(): array
    {
        return get_object_vars($this);
    }
}
