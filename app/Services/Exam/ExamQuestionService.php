<?php

namespace App\Services\Exam;

use App\Models\Exam\ExamQuestion;

class ExamQuestionService
{
    public function getById(int $id): ?ExamQuestion
    {
        return ExamQuestion::find($id);
    }

    public function deleteById(int $id): bool
    {
        return ExamQuestion::findOrFail($id)->delete() === true;
    }

    public function create(array $data, int $createBy): int
    {
        $data['created_by'] = $createBy;

        return ExamQuestion::create($data)->id;
    }

    public function update(int $id, array $data): bool
    {
        return ExamQuestion::findOrFail($id)->update($data);
    }
}
