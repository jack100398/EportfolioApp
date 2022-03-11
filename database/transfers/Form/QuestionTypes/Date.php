<?php

namespace Database\Transfers\Form\QuestionTypes;

use Database\Transfers\Form\QuestionTypes\Interfaces\IBaseQuestionType;

class Date implements IBaseQuestionType
{
    private array $targets = [];

    private bool $require = false;

    private array $customAttributes;

    public function transferQuestion(array $transferDatas): array
    {
        $this->targets = (! empty($transferDatas['targe'])) ? $transferDatas['targe'] : $this->targets;
        $this->require = $transferDatas['question']['ques_required'];
        $object = new DateCustomAttribute($transferDatas);
        $this->customAttributes = $object->transferCustomAttribute($transferDatas);

        return $this->transferObjectToArray();
    }

    private function transferObjectToArray(): array
    {
        $objectToArray = get_object_vars($this);

        return $objectToArray;
    }
}
