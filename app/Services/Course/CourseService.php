<?php

namespace App\Services\Course;

use App\Models\Course\Course;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class CourseService
{
    public function getManyByPagination(int $perPage): LengthAwarePaginator
    {
        return Course::with('members.user')
            ->with('unit')
            ->with('trainingProgramCategory.trainingProgram')
            ->with('courseTarget')
            ->with('courseAssessment')
            ->orderBy('created_at', 'ASC')
            ->paginate($perPage);
    }

    public function getPaginationByIds(Collection $courseIds): LengthAwarePaginator
    {
        return Course::with('members.user')
            ->with('unit')
            ->with('trainingProgramCategory.trainingProgram')
            ->with('courseTarget')
            ->with('courseAssessment')
            ->whereIn('id', $courseIds)
            ->orderBy('created_at', 'ASC')
            ->paginate(10);
    }

    public function create(array $data): int
    {
        return Course::create($data)->id;
    }

    public function update(int $courseId, array $data): bool
    {
        return Course::findOrFail($courseId)->update($data) === true;
    }

    public function delete(int $courseId): bool
    {
        return Course::findOrFail($courseId)->delete() === true;
    }

    public function restoreCourseById(int $courseId): bool
    {
        return Course::withTrashed()
            ->findOrFail($courseId)
            ->restore();
    }

    public function getCourseById(int $courseId): Course
    {
        return Course::findOrFail($courseId);
    }

    public function getCoursesByRequest(array $condition): Collection
    {
        $courses = Course::where('year', $condition['year'])
            ->where('unit_id', $condition['unit_id'])
            ->whereIn('course_mode', $condition['course_mode']);

        isset($condition['start_at']) ?? $courses->where('start_at', '>=', $condition['start_at']);
        isset($condition['end_at']) ?? $courses->where('end_at', '<=', $condition['end_at']);

        return $courses->pluck('id');
    }

    public function trimCourseListByContentAsCourseName(
        Collection $courseIds,
        string $content
    ): Collection {
        return Course::whereIn('id', $courseIds->toArray())
            ->where('course_name', 'like', '%'.$content.'%')
            ->pluck('id');
    }

    public function trimCourseListByCredit(Collection $courseIds, array $credit): Collection
    {
        return Course::whereIn('id', $courseIds->toArray())
            ->get()
            ->filter(function ($course) use ($credit) {
                $courseCredit = [
                    $course->metadata['continuing_credit'],
                    $course->metadata['hospital_credit'],
                ];

                return count(array_intersect($courseCredit, $credit)) > 0;
            })->map(function ($course) {
                return $course['id'];
            });
    }

    public function createCourseMetaData(array $data): Collection
    {
        return collect($data)->map(function ($value) {
            return $value;
        });
    }

    public function shareCourseToCategory(int $courseId, int $programCategoryId): void
    {
        Course::findOrFail($courseId)->courseShares()->attach($programCategoryId);
    }

    public function cloneCourses(Collection $courses, array $categoriesMap): Collection
    {
        $newCourses = $courses->map(function (Course $c) use ($categoriesMap) {
            $newCourse = $c->replicate();
            $newCourse->program_category_id = $categoriesMap[$c->program_category_id];
            $newCourse->save();

            return $newCourse;
        });

        return $newCourses;
    }
}
