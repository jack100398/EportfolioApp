<?php

namespace App\Services\Course;

use App\Models\Course\AssessmentType;

class CourseFormAuthService
{
    public function getById(int $id): AssessmentType
    {
        return AssessmentType::findOrFail($id);
    }

    public function create(array $data): int
    {
        return AssessmentType::create($data)->id;
    }

    public function update(int $id, array $data): void
    {
        AssessmentType::findOrFail($id)->update($data);
    }

    public function delete(int $id): void
    {
        AssessmentType::findOrFail($id)->delete();
    }
}
