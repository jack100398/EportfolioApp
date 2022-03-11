<?php

namespace App\Models\Exam\QuestionMetadata;

class TrueFalseOption implements IOption
{
    private const VALID_ANSWERS = [true, false];

    private bool $answer;

    // 如果傳入的answer不是boolean，則為invalid
    private bool $is_valid;

    public function __construct(array $metadata)
    {
        $answer = $metadata['answer'][0];
        $this->answer = $answer;
        $this->is_valid = is_bool($answer);
    }

    public function verify(): bool
    {
        return $this->is_valid && collect(self::VALID_ANSWERS)->containsStrict(true);
    }

    public function getAnswer(): array
    {
        return [$this->answer];
    }

    public function getOption(): array
    {
        return ['False', 'True'];
    }

    public function getMetadata(): array
    {
        return [
            'option' => ['False', 'True'],
            'answer' => [$this->answer],
        ];
    }

    public function canMarkScore(): bool
    {
        return true;
    }

    public function isCorrect(array $answers): bool
    {
        return $answers === [$this->answer];
    }

    public function showOptions(): array
    {
        return [false, true];
    }
}
