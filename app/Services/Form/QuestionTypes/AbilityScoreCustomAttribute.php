<?php

namespace App\Services\Form\QuestionTypes;

class AbilityScoreCustomAttribute
{
    private int $range = 0;

    private int $max = 0;

    private int $defaultValue = 0;

    private bool $showNAOption = false;

    private int $questionStyle = 1;

    public function transferCustomAttribute(array $customAttributes): array
    {
        $this->transferAttribute($customAttributes);

        return $this->transferObjectToArray();
    }

    private function transferAttribute(array $attribute): void
    {
        $requestKeys = ['range', 'max', 'defaultValue', 'showNAOption', 'questionStyle'];

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
