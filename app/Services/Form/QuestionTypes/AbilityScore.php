<?php

namespace App\Services\Form\QuestionTypes;

use App\Services\Form\QuestionTypes\Interfaces\IBaseQuestionType;

class AbilityScore implements IBaseQuestionType
{
    private int $max = 0;

    private int $min = 0;

    private string $title = '';

    private array $targets = [];

    private bool $require = false;

    private array $customAttributes;

    public function transferQuestion(array $attribute): array
    {
        $this->transferAttribute($attribute);

        return $this->transferObjectToArray();
    }

    private function transferAttribute(array $attribute): void
    {
        $object = new AbilityScoreCustomAttribute();
        $requestKeys = ['title', 'require', 'max', 'min', 'targets', 'customAttributes'];

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
