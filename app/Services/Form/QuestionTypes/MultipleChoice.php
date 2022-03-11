<?php

namespace App\Services\Form\QuestionTypes;

use App\Services\Form\QuestionTypes\Interfaces\IBaseQuestionType;

class MultipleChoice extends BaseQuestionTypeOption implements IBaseQuestionType
{
    private array $targets = [];

    private array $options = [];

    private string $title = '';

    private bool $require = false;

    public function transferQuestion(array $attribute): array
    {
        $this->transferAttribute($attribute);

        return $this->transferObjectToArray();
    }

    private function transferAttribute(array $attribute): void
    {
        $requestKeys = ['title', 'require', 'targets', 'options'];

        collect($requestKeys)->map(function ($requestKey) use ($attribute) {
            if (isset($attribute[$requestKey])) {
                $this->$requestKey = $requestKey === 'options' ?
                 $this->transferOption($attribute['options'])
                : $attribute[$requestKey];
            }
        });
    }

    private function transferObjectToArray(): array
    {
        return get_object_vars($this);
    }
}
