<?php

namespace App\Models\Exam\QuestionMetadata;

interface IOption
{
    public function __construct(array $metadata);

    public function verify(): bool;

    public function getAnswer(): array;

    public function getOption(): array;

    public function getMetadata(): array;

    public function isCorrect(array $answers): bool;

    public function canMarkScore(): bool;

    /**
     * 只顯示作答時會給的選項，如填空題會再給答案數量.
     *
     * @return array
     */
    public function showOptions(): array;
}
