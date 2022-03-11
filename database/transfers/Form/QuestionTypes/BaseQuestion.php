<?php

namespace Database\Transfers\Form\QuestionTypes;

class BaseQuestion
{
    protected int $type;

    protected array $attributes = [];

    protected function transferBaseQuestionType(): array
    {
        return get_object_vars($this);
    }
}
