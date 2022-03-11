<?php

namespace App\Models\Exam\QuestionMetadata;

use Exception;

class EssayOption implements IOption
{
    private array $metadata;

    public function __construct(array $metadata)
    {
        $this->metadata = $metadata;
    }

    public function verify(): bool
    {
        return true;
    }

    public function getAnswer(): array
    {
        return [];
    }

    public function getOption(): array
    {
        return [];
    }

    public function getMetadata(): array
    {
        return [
            'option' => [],
            'answer' => [],
        ];
    }

    public function canMarkScore(): bool
    {
        return false;
    }

    public function isCorrect(array $answers): bool
    {
        throw new Exception('ActionNotSupportedException');
    }

    public function showOptions(): array
    {
        return [];
    }
}
