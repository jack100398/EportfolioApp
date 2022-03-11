<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\Course\CoursePlaceRequest;
use App\Models\Course\CoursePlace;
use App\Services\Course\CoursePlaceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CoursePlaceController extends BaseApiController
{
    private CoursePlaceService $service;

    public function __construct(CoursePlaceService $service)
    {
        $this->service = $service;
    }

    public function index(): JsonResponse
    {
        return $this->respondOk($this->service->getManyByPagination(10));
    }

    public function getByParentId(int $parentId): JsonResponse
    {
        return $this->respondOk($this->service->getPlaceByParentId($parentId));
    }

    public function show(int $id): JsonResponse
    {
        return $this->respondOk($this->service->getById($id));
    }

    public function store(CoursePlaceRequest $request): JsonResponse
    {
        return $this->respondCreated($this->service->create($request->all()));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->service->delete($id);

        return $this->respondNoContent();
    }

    public function update(int $id, CoursePlaceRequest $request): JsonResponse
    {
        $this->service->update($id, $request->all());

        return $this->respondNoContent();
    }
}
