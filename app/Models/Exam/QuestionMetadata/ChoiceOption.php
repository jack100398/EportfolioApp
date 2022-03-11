<?php

namespace App\Models\Exam\QuestionMetadata;

use Illuminate\Support\Collection;

class ChoiceOption implements IOption
{
    private Collection $answers;

    private Collection $options;

    public function __construct(array $metadata)
    {
        $this->answers = collect($metadata['answer']);
        $this->options = collect($metadata['option']);
    }

    public function verify(): bool
    {
        return $this->hasAnswerAndOption() && $this->isAnswerValid();
    }

    public function getAnswer(): array
    {
        return $this->answers->toArray();
    }

    public function getOption(): array
    {
        return $this->options->toArray();
    }

    public function getMetadata(): array
    {
        return [
            'option' => $this->options->toArray(),
            'answer' => $this->answers->toArray(),
        ];
    }

    public function canMarkScore(): bool
    {
        return true;
    }

    public function isCorrect(array $answers): bool
    {
        return collect($answers)->diff($this->answers)->count() === 0;
    }

    public function showOptions(): array
    {
        return $this->options->toArray();
    }

    private function isAnswerValid(): bool
    {
        return $this->answers
            ->filter(fn ($answer) => $answer < 0 || $answer >= $this->options->count())
            ->count() === 0;
    }

    private function hasAnswerAndOption(): bool
    {
        return $this->options->count() > 0 && $this->answers->count() > 0;
    }
}
