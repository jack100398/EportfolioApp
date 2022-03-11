<?php

namespace Database\Transfers\Form\QuestionTypes;

use Database\Transfers\Form\QuestionTypes\Interfaces\IBaseQuestionType;

class Question implements IBaseQuestionType
{
    private string $title = '';

    //允許輸入文字
    private bool $isText = false;

    public function transferQuestion(array $transferDatas): array
    {
        $this->title = $transferDatas['question']['ques_content'];
        $this->isText = $transferDatas['question']['ques_type'] === 0 ? false : true;

        return $this->transferObjectToArray();
    }

    private function transferObjectToArray(): array
    {
        $objectToArray = get_object_vars($this);

        return $objectToArray;
    }
}
