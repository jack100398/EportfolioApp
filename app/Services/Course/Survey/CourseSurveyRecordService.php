<?php

namespace App\Services\Course\Survey;

use App\Models\Course\Survey\CourseSurveyRecord;

class CourseSurveyRecordService
{
    public function getById(int $id): CourseSurveyRecord
    {
        return CourseSurveyRecord::findOrFail($id);
    }

    public function update(int $id, array $data): bool
    {
        return CourseSurveyRecord::findOrFail($id)->update($data) === true;
    }

    public function create(array $data): int
    {
        return CourseSurveyRecord::create($data)->id;
    }

    public function delete(int $id): bool
    {
        return CourseSurveyRecord::findOrFail($id)->delete() === true;
    }
}
