<?php

namespace App\Services\Form\QuestionTypes;

use App\Services\Form\QuestionTypes\Interfaces\IBaseQuestionType;

class Signature implements IBaseQuestionType
{
    public function transferQuestion(array $question): array
    {
        return $this->transferObjectToArray();
    }

    private function transferObjectToArray(): array
    {
        return get_object_vars($this);
    }
}
