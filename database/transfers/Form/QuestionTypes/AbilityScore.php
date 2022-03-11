<?php

namespace Database\Transfers\Form\QuestionTypes;

use Database\Transfers\Form\QuestionTypes\Interfaces\IBaseQuestionType;

class AbilityScore implements IBaseQuestionType
{
    private int $max = 0;

    private int $min = 0;

    private array $targets = [];

    private bool $require = false;

    private string $title = '';

    private array $customAttributes;

    public function transferQuestion(array $transferDatas): array
    {
        $this->require = $transferDatas['question']['ques_required'];

        $this->min = $transferDatas['question']['ques_min_number'];

        $this->max = $transferDatas['question']['ques_max_number'];

        $this->targets = (! empty($transferDatas['targe'])) ? $transferDatas['targe'] : $this->targets;

        $this->title = $transferDatas['question']['ques_content'];

        $object = new AbilityScoreCustomAttribute();
        $this->customAttributes = $object->transferCustomAttribute($transferDatas);

        return $this->transferObjectToArray();
    }

    private function transferObjectToArray(): array
    {
        $objectToArray = get_object_vars($this);

        return $objectToArray;
    }
}
