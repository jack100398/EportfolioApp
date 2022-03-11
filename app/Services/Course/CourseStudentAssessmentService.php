<?php

namespace App\Services\Course;

use App\Models\Course\CourseStudentAssessment;
use Illuminate\Support\Collection;

class CourseStudentAssessmentService
{
    public function create(array $data): int
    {
        return CourseStudentAssessment::create($data)->id;
    }

    public function update(int $id, array $data): bool
    {
        return CourseStudentAssessment::findOrFail($id)->update($data) === true;
    }

    public function delete(int $id): bool
    {
        return CourseStudentAssessment::findOrFail($id)->delete() === true;
    }

    public function getById(int $id): CourseStudentAssessment
    {
        return CourseStudentAssessment::findOrFail($id);
    }

    public function getByCourseId(int $courseId): Collection
    {
        return CourseStudentAssessment::where('course_id', $courseId)->get();
    }

    public function getByStudentId(int $studentId): Collection
    {
        return CourseStudentAssessment::where('student_id', $studentId)->get();
    }
}
