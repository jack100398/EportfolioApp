<?php

namespace App\Services\Course\Survey;

use App\Models\Course\Survey\CourseSurvey;

class CourseSurveyService
{
    public function getById(int $id): CourseSurvey
    {
        return CourseSurvey::findOrFail($id);
    }

    public function update(int $id, array $data): bool
    {
        return CourseSurvey::findOrFail($id)->update($data) === true;
    }

    public function create(array $data): int
    {
        return CourseSurvey::create($data)->id;
    }

    public function delete(int $id): bool
    {
        return CourseSurvey::findOrFail($id)->delete() === true;
    }
}
