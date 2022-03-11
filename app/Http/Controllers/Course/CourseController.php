<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\Course\CreateCourseRequest;
use App\Http\Requests\Course\SearchCourseRequest;
use App\Http\Requests\Course\UpdateCourseRequest;
use App\Services\Course\CourseAssessmentService;
use App\Services\Course\CourseHistoryService;
use App\Services\Course\CourseMemberService;
use App\Services\Course\CourseService;
use App\Services\Course\CourseTargetService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class CourseController extends BaseApiController
{
    private CourseService $courseService;

    private CourseAssessmentService $courseAssessmentService;

    private CourseMemberService $courseMemberService;

    private CourseHistoryService $courseHistoryService;

    private CourseTargetService $courseTargetService;

    public function __construct(
        CourseService $courseService,
        CourseMemberService $courseMemberService,
        CourseAssessmentService $courseAssessmentService,
        CourseHistoryService $courseHistoryService,
        CourseTargetService $courseTargetService
    ) {
        $this->courseService = $courseService;
        $this->courseMemberService = $courseMemberService;
        $this->courseAssessmentService = $courseAssessmentService;
        $this->courseHistoryService = $courseHistoryService;
        $this->courseTargetService = $courseTargetService;
    }

    public function index(request $request): JsonResponse
    {
        return $this->respondOk($this->courseService->getManyByPagination((int) $request->query('size')));
    }

    public function search(SearchCourseRequest $request): JsonResponse
    {
        $courseIds = $this->courseService->getCoursesByRequest($request->all());

        $courseIds = $this->courseAssessmentService
            ->trimCourseListByAssessment($courseIds, $request->assessment_id);

        $courseIds = $this->trimCourseByContent($courseIds, $request->searchContent);

        $courseIds = $this->courseService
            ->trimCourseListByCredit($courseIds, $request->credit);

        return $this->respondOk($this->courseService->getPaginationByIds($courseIds));
    }

    public function show(int $courseId): JsonResponse
    {
        $course = $this->courseService->getCourseById($courseId);

        return $this->respondOk($course);
    }

    public function store(CreateCourseRequest $request): JsonResponse
    {
        // $metadataColumn = ['course_target', 'people_limit', 'combine_course', 'other_teacher', 'continuing_credit', 'hospital_credit'];

        $data = $this->packageCreateData($request);

        // $data['metadata'] = $this->courseService
        //     ->createCourseMetaData($request->only($metadataColumn));

        $id = $this->courseService->create($data);

        $this->courseAssessmentService->createCourseAssessments($id, $request->assessment);

        $this->courseMemberService
            ->createCourseTeacher($id, $request->teachers, $request->user()->id);

        $this->courseMemberService
            ->createCourseStudent($id, $request->students, $request->user()->id);

        // $this->courseHistoryService->create($id, $data);

        return $this->respondCreated($id);
    }

    public function update(int $courseId, UpdateCourseRequest $request): JsonResponse
    {
        $data = $this->packageUpdateData($request);
        $this->courseService->update($courseId, $data);
        // $this->courseHistoryService->create($courseId, $data);

        return $this->respondNoContent();
    }

    public function destroy(int $id): JsonResponse
    {
        $this->courseService->delete($id);

        return $this->respondNoContent();
    }

    public function getCourseTargetList(): JsonResponse
    {
        return $this->respondOk($this->courseTargetService->getAll());
    }

    public function share(int $id, int $programCategoryId): JsonResponse
    {
        $this->courseService->shareCourseToCategory($id, $programCategoryId);

        return $this->respondNoContent();
    }

    private function trimCourseByContent(Collection $courseIds, string $content): Collection
    {
        if ($courseIds->contains($content)) {
            return collect($content);
        }

        $teacherCourseIds = $this->courseMemberService->searchTeacherCourseByContent($courseIds, $content);

        $courseNameIds = $this->courseService->trimCourseListByContentAsCourseName($courseIds, $content);

        return collect(array_merge($teacherCourseIds->toArray(), $courseNameIds->toArray()));
    }

    private function packageCreateData(CreateCourseRequest $request): array
    {
        $data = $request->all();
        $data['created_by'] = $request->user()->id;
        $data['updated_by'] = $request->user()->id;

        return $data;
    }

    private function packageUpdateData(UpdateCourseRequest $request): array
    {
        $data = $request->all();
        $data['updated_by'] = $request->user()->id;

        return $data;
    }
}
