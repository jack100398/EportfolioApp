<?php

namespace App\Models\Exam\QuestionMetadata;

use Exception;

class FillOption implements IOption
{
    private array $answers;

    public function __construct(array $metadata)
    {
        $this->answers = $metadata['answer'];
    }

    public function verify(): bool
    {
        return count($this->answers) > 0;
    }

    public function getAnswer(): array
    {
        return $this->answers;
    }

    public function getOption(): array
    {
        throw new Exception('ActionNotSupportedException');
    }

    public function getMetadata(): array
    {
        return [
            'option' => [],
            'answer' => [$this->answers],
        ];
    }

    public function canMarkScore(): bool
    {
        return true;
    }

    public function isCorrect(array $answers): bool
    {
        return $answers === $this->answers;
    }

    public function showOptions(): array
    {
        // 回傳有幾格要填寫
        return [count($this->answers)];
    }
}
