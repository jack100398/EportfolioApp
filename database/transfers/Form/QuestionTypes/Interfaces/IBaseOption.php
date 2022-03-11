<?php

namespace Database\Transfers\Form\QuestionTypes\Interfaces;

interface IBaseOption extends IBaseQuestionType
{
    public function pushOption(array $question, array $option): array;
}
