<?php

namespace App\Services\Form\QuestionTypes;

class DateCustomAttribute
{
    private int $questionStyle = 1;

    public function transferCustomAttribute(array $attribute): array
    {
        $this->transferAttribute($attribute);

        return $this->transferObjectToArray();
    }

    private function transferAttribute(array $attribute): void
    {
        $requestKeys = ['questionStyle'];

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
