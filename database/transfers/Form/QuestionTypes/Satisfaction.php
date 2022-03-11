<?php

namespace Database\Transfers\Form\QuestionTypes;

use Database\Transfers\Form\QuestionTypes\Interfaces\IBaseQuestionType;

class Satisfaction implements IBaseQuestionType
{
    private int $max = 0;

    private array $targets = [];

    private bool $require = false;

    private string $title = '';

    protected array $customAttributes;

    public function transferQuestion(array $transferDatas): array
    {
        $this->title = $transferDatas['question']['ques_content'];
        $this->targets = (! empty($transferDatas['targe'])) ? $transferDatas['targe'] : $this->targets;
        $this->max = $transferDatas['question']['ques_max_number'];
        $this->require = $transferDatas['question']['ques_required'];
        $object = new SatisfactionCustomAttribute($transferDatas);
        $this->customAttributes = $object->transferCustomAttribute($transferDatas);

        return $this->transferObjectToArray();
    }

    private function transferObjectToArray(): array
    {
        $objectToArray = get_object_vars($this);

        return $objectToArray;
    }
}
