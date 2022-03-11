<?php

namespace Database\Transfers\Form\QuestionTypes;

class AbilityScoreCustomAttribute
{
    private int $range = 0;

    private int $max = 0;

    private int $defaultValue = 0;

    private bool $showNAOption = false;

    private int $questionStyle = 1;

    public function transferCustomAttribute(array $transferDatas)
    {
        $custom_attr = json_decode($transferDatas['question']['custom_attr']);

        $this->range = (isset($custom_attr->range) &&
        is_numeric($custom_attr->range)) ?? $custom_attr->range;

        $this->max = (isset($custom_attr->max)) ?? $custom_attr->max;

        $this->defaultValue = (isset($custom_attr->default) &&
         is_numeric($custom_attr->default)) ?? $custom_attr->default;

        $this->showNAOption = (isset($custom_attr->show_NA_option) &&
         is_bool($custom_attr->show_NA_option)) ?? $custom_attr->show_NA_option;

        $this->questionStyle = ($transferDatas['question']['ques_class'] !== null) ??
        $transferDatas['question']['ques_class'];

        return $this->transferObjectToArray();
    }

    private function transferObjectToArray(): array
    {
        $objectToArray = get_object_vars($this);

        return $objectToArray;
    }
}
