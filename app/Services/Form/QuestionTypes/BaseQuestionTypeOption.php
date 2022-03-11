<?php

namespace App\Services\Form\QuestionTypes;

use App\Services\Form\QuestionTypeFactory;

class BaseQuestionTypeOption
{
    protected function transferOption(array $options): array
    {
        return array_map(function ($option) {
            $object = new QuestionTypeFactory();

            return $object->getQuestionType($option);
        }, $options);
    }
}
