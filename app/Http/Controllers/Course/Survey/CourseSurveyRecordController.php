<?php

namespace App\Http\Controllers\Course\Survey;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\Course\Survey\CreateCourseSurveyRecordRequest;
use App\Http\Requests\Course\Survey\UpdateCourseSurveyRecordRequest;
use App\Services\Course\Survey\CourseSurveyRecordService;
use App\Services\Course\Survey\CourseSurveyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;

class CourseSurveyRecordController extends BaseApiController
{
    private CourseSurveyService $courseSurveyService;

    private CourseSurveyRecordService $recordService;

    public function __construct(
        CourseSurveyService $courseSurveyService,
        CourseSurveyRecordService $recordService
    ) {
        $this->courseSurveyService = $courseSurveyService;
        $this->recordService = $recordService;
    }

    public function show(int $id): JsonResponse
    {
        return $this->respondOk($this->recordService->getById($id));
    }

    public function store(CreateCourseSurveyRecordRequest $request): JsonResponse
    {
        $id = $this->recordService->create($this->makeRecordData($request)->toArray());

        return $this->respondCreated($id);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->recordService->delete($id);

        return $this->respondNoContent();
    }

    public function update(int $id, UpdateCourseSurveyRecordRequest $request): JsonResponse
    {
        $this->recordService->update($id, $request->all());

        return $this->respondNoContent();
    }

    private function makeRecordData(CreateCourseSurveyRecordRequest $request): Collection
    {
        $data = collect($request->all());

        $data->put('answered_by', $request->user()->id);

        return $data;
    }
}
