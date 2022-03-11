<?php

namespace Database\Transfers\Form\QuestionTypes;

class DateCustomAttribute
{
    private int $questionStyle = 1;

    public function transferCustomAttribute(array $transferDatas)
    {
        $this->questionStyle = ($transferDatas['question']['ques_class'] !== null)
         ?? $transferDatas['question']['ques_class'];

        return $this->transferObjectToArray();
    }

    private function transferObjectToArray(): array
    {
        $objectToArray = get_object_vars($this);

        return $objectToArray;
    }
}
