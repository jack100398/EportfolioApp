<?php

namespace App\Services\Course;

use App\Models\Course\CourseAssessment;
use Illuminate\Support\Collection;

class CourseAssessmentService
{
    public function trimCourseListByAssessment(
        Collection $courseIds,
        array $assessmentIds
    ): Collection {
        return CourseAssessment::whereIn('course_id', $courseIds->toArray())
            ->whereIn('assessment_id', $assessmentIds)
            ->pluck('course_id');
    }

    public function createCourseAssessments(int $courseId, array $assessments): void
    {
        collect($assessments)->each(function ($assessmentData, $assessmentId) use ($courseId) {
            $data = [
                'course_id' => $courseId,
                'assessment_id' => $assessmentId,
                'data' => $assessmentData,
            ];
            CourseAssessment::create($data);
        });
    }
}
