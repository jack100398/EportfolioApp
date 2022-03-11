<?php

namespace Database\Transfers\Form\QuestionTypes;

use Database\Transfers\Form\QuestionTypes\Interfaces\IBaseQuestionType;

class Canvas implements IBaseQuestionType
{
    private string $title = '';

    private array $targets = [];

    private bool $require = false;

    public function transferQuestion(array $transferDatas): array
    {
        $this->targets = (! empty($transferDatas['targe'])) ? $transferDatas['targe'] : $this->targets;
        $this->require = $transferDatas['question']['ques_required'];
        $this->title = $transferDatas['question']['ques_content'];

        return $this->transferObjectToArray();
    }

    private function transferObjectToArray(): array
    {
        $objectToArray = get_object_vars($this);

        return $objectToArray;
    }
}
