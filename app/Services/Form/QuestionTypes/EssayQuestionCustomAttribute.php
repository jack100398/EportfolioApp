<?php

namespace App\Services\Form\QuestionTypes;

class EssayQuestionCustomAttribute
{
    private int $minLength = 0;

    private int $questionStyle = 1;

    public function transferCustomAttribute(array $customAttributes): array
    {
        $this->transferAttribute($customAttributes);

        return $this->transferObjectToArray();
    }

    private function transferAttribute(array $attribute): void
    {
        $requestKeys = ['minLength', 'questionStyle'];

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
