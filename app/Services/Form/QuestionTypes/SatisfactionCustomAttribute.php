<?php

namespace App\Services\Form\QuestionTypes;

class SatisfactionCustomAttribute
{
    private bool $hasNA = false;

    public function transferCustomAttribute(array $attribute): array
    {
        $this->hasNA = $attribute['hasNA'] ?? false;

        return $this->transferObjectToArray();
    }

    private function transferObjectToArray(): array
    {
        return get_object_vars($this);
    }
}
