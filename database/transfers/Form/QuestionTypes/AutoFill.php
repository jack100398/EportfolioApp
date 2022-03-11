<?php

namespace Database\Transfers\Form\QuestionTypes;

use Database\Transfers\Form\QuestionTypes\Interfaces\IBaseQuestionType;

/**
 * 在顯示題目時，系統會自動抓取要顯得項目.
 */
class AutoFill implements IBaseQuestionType
{
    private string $title = '';

    private int $value = 0;

    private array $targets = [];

    public function transferQuestion(array $transferDatas): array
    {
        $this->title = $transferDatas['question']['ques_content'];
        $this->value = $transferDatas['question']['ques_text'];
        $this->targets = (! empty($transferDatas['targe'])) ? $transferDatas['targe'] : $this->targets;

        return $this->transferObjectToArray();
    }

    private function transferObjectToArray(): array
    {
        $objectToArray = get_object_vars($this);

        return $objectToArray;
    }
}
