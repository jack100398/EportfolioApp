<?php

namespace App\Services\Course\Survey;

use App\Models\Course\Survey\SurveyQuestion;

class SurveyQuestionService
{
    public function getById(int $id): SurveyQuestion
    {
        return SurveyQuestion::findOrFail($id);
    }

    public function update(int $id, array $data): bool
    {
        return SurveyQuestion::findOrFail($id)->update($data) === true;
    }

    public function create(array $data): int
    {
        return SurveyQuestion::create($data)->id;
    }

    public function delete(int $id): bool
    {
        return SurveyQuestion::findOrFail($id)->delete() === true;
    }
}
