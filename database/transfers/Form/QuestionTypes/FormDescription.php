<?php

namespace Database\Transfers\Form\QuestionTypes;

use Database\Transfers\Form\QuestionTypes\Interfaces\IBaseQuestionType;

class FormDescription implements IBaseQuestionType
{
    private string $content = '說明區塊';

    public function transferQuestion(array $transferDatas): array
    {
        $this->content = $transferDatas['question']['ques_content'];

        return $this->transferObjectToArray();
    }

    private function transferObjectToArray(): array
    {
        $objectToArray = get_object_vars($this);

        return $objectToArray;
    }
}
