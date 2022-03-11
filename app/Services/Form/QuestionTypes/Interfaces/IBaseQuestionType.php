<?php

namespace App\Services\Form\QuestionTypes\Interfaces;

interface IBaseQuestionType
{
    public function transferQuestion(array $question): array;
}
