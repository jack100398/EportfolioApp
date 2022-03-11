<?php

namespace App\Services\Course;

use App\Models\Auth\User;
use App\Models\Course\CourseMember;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class CourseMemberService
{
    public function getManyByPagination(int $perPage): LengthAwarePaginator
    {
        return CourseMember::orderBy('created_at', 'DESC')
            ->paginate($perPage);
    }

    public function getMembersByCourseId(int $courseId): Collection
    {
        return CourseMember::where('course_id', $courseId)->get('id');
    }

    public function update(int $id, array $data): bool
    {
        return CourseMember::findOrFail($id)->update($data) === true;
    }

    public function createMemberByRequest(array $request): int
    {
        return CourseMember::create($request)->id;
    }

    public function deleteById(int $id): bool
    {
        return CourseMember::findOrFail($id)->delete() === true;
    }

    public function createCourseTeacher(int $courseId, array $teachers, int $creator): void
    {
        collect($teachers)->each(function ($data) use ($courseId, $creator) {
            $teacherData = $this->packageMemberData($courseId, $data['id'], $creator);
            $teacherData['role'] = $data['role'];
            CourseMember::create($teacherData);
        });
    }

    public function createCourseStudent(int $courseId, array $students, int $creator): void
    {
        collect($students)->each(function ($isOnlineCourse, $userId) use ($courseId, $creator) {
            $studentData = $this->packageMemberData($courseId, (int) $userId, $creator);
            $studentData['is_online_course'] = $isOnlineCourse;
            CourseMember::create($studentData);
        });
    }

    public function searchTeacherCourseByContent(Collection $courseIds, string $content): Collection
    {
        $users = User::where('name', 'like', '%'.$content.'%')
            ->pluck('id');

        return CourseMember::whereIn('course_id', $courseIds->toArray())
            ->whereIn('user_id', $users)
            ->where('role', '>', 1)
            ->pluck('course_id');
    }

    private function packageMemberData(int $courseId, int $memberId, int $creator): array
    {
        return [
            'course_id' => $courseId,
            'user_id' => $memberId,
            'role' => 1,
            'is_online_course' => 0,
            'updated_by' => $creator,
            'state' => 0,
        ];
    }
}
