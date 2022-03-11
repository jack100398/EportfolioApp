<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\Course\CreateAssessmentTypeRequest;
use App\Http\Requests\Course\UpdateCourseFormAuthRequest;
use App\Models\Course\AssessmentType;
use App\Services\Course\CourseFormAuthService;
use App\Services\Form\FormUnitService;
use Illuminate\Http\JsonResponse;

class CourseFormAuthController extends BaseApiController
{
    private CourseFormAuthService $service;

    private FormUnitService $formUnitService;

    public function __construct(
        CourseFormAuthService $service,
        FormUnitService $formUnitService
    ) {
        $this->service = $service;
        $this->formUnitService = $formUnitService;
    }

    public function show(int $unitId): JsonResponse
    {
        $formIds = $this->formUnitService->getByUnitId($unitId);

        return $this->respondOk(
            AssessmentType::WhereIn('source', $formIds)
                ->pluck('id')
        );
    }

    public function store(CreateAssessmentTypeRequest $request): JsonResponse
    {
        return $this->respondCreated($this->service->create($request->all()));
    }

    public function update(int $id, UpdateCourseFormAuthRequest $request): JsonResponse
    {
        $this->service->update($id, $request->all());

        return $this->respondNoContent();
    }

    public function destroy(int $id): JsonResponse
    {
        $this->service->delete($id);

        return $this->respondNoContent();
    }
}
