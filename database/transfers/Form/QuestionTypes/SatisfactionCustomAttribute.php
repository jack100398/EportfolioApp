<?php

namespace Database\Transfers\Form\QuestionTypes;

class SatisfactionCustomAttribute
{
    private bool $hasNA = false;

    public function transferCustomAttribute(array $transferDatas)
    {
        $custom_attr = json_decode($transferDatas['question']['custom_attr']);

        $this->hasNA = isset($custom_attr->has_na) ?? $custom_attr->has_na;

        return $this->transferObjectToArray();
    }

    private function transferObjectToArray(): array
    {
        $objectToArray = get_object_vars($this);

        return $objectToArray;
    }
}
