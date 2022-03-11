<?php

namespace Database\Transfers\Form\QuestionTypes;

use Database\Transfers\Form\QuestionTypes\Interfaces\IBaseQuestionType;

class CalculateScore implements IBaseQuestionType
{
    private int $max = 0;

    private array $targets = [];

    private bool $require = false;

    private string $title = '';

    public function __construct()
    {
        $this->type = 17;
    }

    public function transferQuestion(array $transferDatas): array
    {
        $this->title = $transferDatas['question']['ques_content'];
        $this->max = $transferDatas['question']['ques_max_number'];
        $this->require = $transferDatas['question']['ques_required'];

        $this->targets = (! empty($transferDatas['targe'])) ? $transferDatas['targe'] : $this->targets;

        return $this->transferObjectToArray();
    }

    private function transferObjectToArray(): array
    {
        $objectToArray = get_object_vars($this);

        return $objectToArray;
    }
}
