<?php

namespace Database\Transfers\Form\QuestionTypes\Interfaces;

interface IBaseQuestionType
{
    public function transferQuestion(array $transferDatas): array;
}
