<?php

namespace Database\Transfers\Form\FormQuestionTypeFactory\Interfaces;

use Illuminate\Http\Request;

interface IFormQuestionTypeFactory
{
    public function transferQuestionType(array $transferDatas): array;

    public function pushQuestion(array $questionType, array $question);
}
