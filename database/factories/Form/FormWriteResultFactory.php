<?php

namespace Database\Factories\Form;

class FormWriteResultFactory
{
    private function formDescribe(): array
    {
        return [null];
    }

    private function questionGroup(array $questionTypes): array
    {
        return $this->makeQuestionTypeResult($questionTypes);
    }

    private function image(): array
    {
        return [null];
    }

    private function abilityScore(): array
    {
        return [8];
    }

    /**
     * 系統帶入user name.
     */
    private function autoFill(): array
    {
        return ['user name'];
    }

    private function calculateScore(): array
    {
        return [82.2];
    }

    private function canvas(): array
    {
        return ['file_name'=>'123.jpg', 'extension'=>'jpg'];
    }

    private function date(int $questionStyle): array
    {
        return  $questionStyle === 1 ? ['hour'=>'11', 'minute'=>20] :
          ['date'=>'2021-02-23'];
    }

    private function description(): array
    {
        return [null];
    }

    private function essayQuestion(): array
    {
        return ['我是問答題'];
    }

    private function feedback(): array
    {
        return ['回饋'];
    }

    /**
     * [選項(可能會有複數),"text"=>[選項index=>"填寫結果"](可能是複數)].
     */
    private function multipleChoice(array $options): array
    {
        return $this->generateQuestion(6, $options);
    }

    private function multiSelectQuestion(array $options): array
    {
        return $this->generateQuestion(7, $options);
    }

    private function generateQuestion(int $type, $options): array
    {
        if ($type === 7) {
            $optionTexts = collect($options)->map(function ($option, $key) {
                if ($option->attributes->isText === true) {
                    return [$key + 1=>''];
                }
            })->filter(function ($result) {
                return ! is_null($result);
            })->toArray();

            $selectOptions = collect($options)->map(function ($option, $key) {
                if ($option->attributes->isText !== true) {
                    return [$key + 1];
                }
            })->filter(function ($result) {
                return ! is_null($result);
            })->toArray();

            $selectOptions['text'] = $optionTexts;

            return $selectOptions;
        } else {
            $optionTexts = collect($options)->map(function ($option, $key) {
                if ($option->attributes->isText === true) {
                    return [$key + 1=>''];
                }
            })->filter(function ($result) {
                return ! is_null($result);
            })->toArray();

            $selectOptions = collect($options)->map(function ($option, $key) {
                if ($option->attributes->isText !== true) {
                    return [$key + 1];
                }
            })->filter(function ($result) {
                return ! is_null($result);
            })->toArray();

            if (count($selectOptions) > 0) {
                return [$selectOptions[array_key_first($selectOptions)], 'text'=>$optionTexts];
            } else {
                $key = array_key_first($optionTexts);
                $optionTexts[$key] = '填寫選項';

                return ['text'=>$optionTexts];
            }
        }
    }

    private function satisfaction(): array
    {
        return [5];
    }

    /**
     * 系統自動帶入.
     */
    private function signature(): array
    {
        return [null];
    }

    private function trueOrFalse(array $options): array
    {
        return $this->generateQuestion(18, $options);
    }

    /**
     * file_name
     * extension.
     */
    private function uploadFile(): array
    {
        return ['file_name'=>'a.pdf', 'extension'=>'pdf'];
    }

    private function makeQuestionResult(object $questionType): array
    {
        switch ($questionType->type) {
            case 2:
                return $this->description();
            case 4:
                return $this->formDescribe();
            case 5:
                return $this->questionGroup($questionType->attributes->questions);
            case 6:
                return $this->multipleChoice($questionType->attributes->options);
            case 7:
                return $this->multiSelectQuestion($questionType->attributes->options);
            case 8:
                return $this->essayQuestion();
            case 9:
                return $this->abilityScore();
            case 10:
                return $this->satisfaction();
            case 11:
                return $this->uploadFile();
            case 12:
                return $this->canvas();
            case 13:
                return $this->date($questionType->attributes->customAttributes->questionStyle);
            case 14:
                return $this->feedback();
            case 15:
                return $this->signature();
            case 16:
                return $this->image();
            case 17:
                return $this->calculateScore();
            case 18:
                return $this->trueOrFalse($questionType->attributes->options);
            case 19:
                return $this->autoFill();
        }
    }

    public function makeResult(array $questions): array
    {
        return collect($questions)->map(function ($question) {
            return $this->makeQuestionResult($question);
        })->toArray();
    }

    private function makeQuestionTypeResult(array $questionTypes): array
    {
        return collect($questionTypes)->map(function ($questionType) {
            return $this->makeQuestionResult($questionType);
        })->toArray();
    }
}
