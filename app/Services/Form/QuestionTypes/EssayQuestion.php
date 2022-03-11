<?php

namespace App\Services\Form\QuestionTypes;

use App\Services\Form\QuestionTypes\Interfaces\IBaseQuestionType;

class EssayQuestion implements IBaseQuestionType
{
    private array $targets = [];

    private bool $require = false;

    private string $title = '';

    private array $customAttributes;

    public function transferQuestion(array $attribute): array
    {
        $this->transferAttribute($attribute);

        return $this->transferObjectToArray();
    }

    private function transferAttribute(array $attribute): void
    {
        $object = new EssayQuestionCustomAttribute();

        $requestKeys = ['require', 'targets', 'title', 'customAttributes'];

        collect($requestKeys)->map(function ($requestKey) use ($attribute, $object) {
            if (isset($attribute[$requestKey])) {
                $this->$requestKey = $requestKey === 'customAttributes' ?
                $object->transferCustomAttribute($attribute[$requestKey])
                : $attribute[$requestKey];
            }
        });
    }

    private function transferObjectToArray(): array
    {
        return get_object_vars($this);
    }
}
