<?php

namespace App\Services\Form\QuestionTypes;

use App\Services\Form\QuestionTypes\Interfaces\IBaseQuestionType;

class Description implements IBaseQuestionType
{
    private string $content = '';

    public function transferQuestion(array $attribute): array
    {
        $this->content = $attribute['content'] ?? '說明';

        return $this->transferObjectToArray();
    }

    private function transferObjectToArray(): array
    {
        return get_object_vars($this);
    }
}
