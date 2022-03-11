<?php

namespace App\Services\Form\QuestionTypes;

use App\Services\Form\QuestionTypes\Interfaces\IBaseQuestionType;

class Question implements IBaseQuestionType
{
    private string $title = '';

    //允許輸入文字
    private bool $isText = false;

    public function transferQuestion(array $attribute): array
    {
        $this->title = $attribute['title'] ?? '題目名稱';
        $this->isText = $attribute['isText'] ?? false;

        return $this->transferObjectToArray();
    }

    private function transferObjectToArray(): array
    {
        return get_object_vars($this);
    }
}
