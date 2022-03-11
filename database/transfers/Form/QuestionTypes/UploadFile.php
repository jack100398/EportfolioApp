<?php

namespace Database\Transfers\Form\QuestionTypes;

use Database\Transfers\Form\QuestionTypes\Interfaces\IBaseQuestionType;

class UploadFile implements IBaseQuestionType
{
    private string $title = '';

    private array $targets = [];

    private array $option = [];

    private bool $require = false;

    public function transferQuestion(array $transferDatas): array
    {
        $this->title = $transferDatas['question']['ques_content'];
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
