<?php

namespace Database\Transfers\Form\QuestionTypes;

class EssayQuestionCustomAttribute
{
    private int $minLength = 0;

    private int $questionStyle = 1;

    public function transferCustomAttribute(array $transferDatas)
    {
        $custom_attr = json_decode($transferDatas['question']['custom_attr']);

        $this->minLength = (isset($custom_attr->min_length)) ? (int) $custom_attr->min_length : 0;
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
