<?php

namespace App\Services\Form\QuestionTypes;

use App\Services\Form\QuestionTypes\Interfaces\IBaseQuestionType;

/**
 * 在顯示題目時，系統會自動抓取要顯得項目.
 */
class AutoFill implements IBaseQuestionType
{
    private string $title = '';

    private int $value = 0;

    private array $targets = [];

    public function transferQuestion(array $attribute): array
    {
        $this->transferAttribute($attribute);

        return $this->transferObjectToArray();
    }

    private function transferAttribute(array $attribute): void
    {
        $requestKeys = ['title',  'targets', 'value'];

        collect($requestKeys)->map(function ($requestKey) use ($attribute) {
            if (isset($attribute[$requestKey])) {
                $this->$requestKey = $attribute[$requestKey];
            }
        });
    }

    private function transferObjectToArray(): array
    {
        return get_object_vars($this);
    }
}
