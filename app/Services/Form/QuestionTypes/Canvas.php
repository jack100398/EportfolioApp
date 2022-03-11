<?php

namespace App\Services\Form\QuestionTypes;

use App\Services\Form\QuestionTypes\Interfaces\IBaseQuestionType;

class Canvas implements IBaseQuestionType
{
    private string $title = '';

    private array $targets = [];

    private bool $require = false;

    public function transferQuestion(array $attribute): array
    {
        $this->transferAttribute($attribute);

        return $this->transferObjectToArray();
    }

    private function transferAttribute(array $attribute): void
    {
        $requestKeys = ['title', 'targets', 'require'];

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
