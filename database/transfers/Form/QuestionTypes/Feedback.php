<?php

namespace Database\Transfers\Form\QuestionTypes;

use Database\Transfers\Form\QuestionTypes\Interfaces\IBaseQuestionType;
use Database\Transfers\Form\QuestionTypes\Interfaces\IFeedback;

class Feedback implements IBaseQuestionType
{
    private array $targets = [];

    private string $title = '';

    private bool $require = false;

    public function transferQuestion(array $transferDatas): array
    {
        $this->targets = (! empty($transferDatas['targe'])) ? $transferDatas['targe'] : $this->targets;
        $this->title = $transferDatas['question']['ques_content'];
        $this->require = $transferDatas['question']['ques_required'];

        return $this->transferObjectToArray();
    }

    private function transferObjectToArray(): array
    {
        $objectToArray = get_object_vars($this);

        return $objectToArray;
    }
}
