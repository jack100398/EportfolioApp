<?php

namespace Database\Transfers\Form\QuestionTypes;

use Database\Transfers\Form\QuestionTypes\Interfaces\IBaseOption;

class TrueOrFalse implements IBaseOption
{
    private array $targets = [];

    private array $options = [];

    private bool $require = false;

    private string $title = '';

    public function pushOption(array $question, array $option): array
    {
        $this->require = $question['attributes']['require'];
        $this->targets = $question['attributes']['targets'];
        $this->title = $question['attributes']['title'];
        $this->type = $question['type'];
        $this->options = $option;

        return $this->transferObjectToArray();
    }

    public function transferQuestion(array $transferDatas): array
    {
        $this->require = $transferDatas['question']['ques_required'];
        $this->targets = (! empty($transferDatas['targe'])) ? $transferDatas['targe'] : $this->targets;
        $this->title = $transferDatas['question']['ques_content'];

        return $this->transferObjectToArray();
    }

    private function transferObjectToArray(): array
    {
        $objectToArray = get_object_vars($this);

        return $objectToArray;
    }
}
