<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\Course\CreateCourseMemberRequest;
use App\Http\Requests\Course\UpdateCourseMemberRequest;
use App\Services\Course\CourseMemberService;
use Illuminate\Http\JsonResponse;

class CourseMemberController extends BaseApiController
{
    private CourseMemberService $service;

    public function __construct(CourseMemberService $service)
    {
        $this->service = $service;
    }

    public function index(): JsonResponse
    {
        return $this->respondOk($this->service->getManyByPagination(10));
    }

    public function show(int $courseId): JsonResponse
    {
        return $this->respondOk($this->service->getMembersByCourseId($courseId));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->service->deleteById($id);

        return $this->respondNoContent();
    }

    public function update(int $id, UpdateCourseMemberRequest $request): JsonResponse
    {
        $this->service->update($id, $request->all());

        return $this->respondNoContent();
    }

    public function store(CreateCourseMemberRequest $request): JsonResponse
    {
        return $this->respondCreated(
            $this->service->createMemberByRequest($request->all())
        );
    }
}
