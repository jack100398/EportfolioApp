<?php

namespace App\Services\Course;

use App\Models\Course\Course;
use App\Models\Course\CourseHistory;
use Illuminate\Support\Collection;

class CourseHistoryService
{
    private const CREATE = 1;

    private const UPDATE = 2;

    public function getByCourseId(int $courseId): Collection
    {
        return CourseHistory::where('course_id', $courseId)->get();
    }

    public function create(int $courseId, array $data): int
    {
        $courseData = Course::findOrFail($courseId);

        $backData = $courseData->toArray();

        $backData['overdue_type'] = $data['overdue_type'];
        $backData['overdue_description'] = $data['overdue_description'];
        $backData['request'] = $data;
        $backData['course_id'] = $courseId;
        $backData['back_type'] = isset($data['created_by']) ? self::CREATE : self::UPDATE;

        return CourseHistory::create($backData)->id;
    }
}
