<?php

namespace Database\Transfers\Form\QuestionTypes;

use Database\Transfers\Form\QuestionTypes\Interfaces\IBaseQuestionType;

class Signature implements IBaseQuestionType
{
    public function transferQuestion(array $transferDatas): array
    {
        return $this->transferObjectToArray();
    }

    private function transferObjectToArray(): array
    {
        $objectToArray = get_object_vars($this);

        return $objectToArray;
    }
}
