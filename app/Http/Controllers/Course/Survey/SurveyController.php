<?php

namespace App\Http\Controllers\Course\Survey;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\Course\Survey\CreateSurveyRequest;
use App\Http\Requests\Course\Survey\UpdateSurveyRequest;
use App\Services\Course\Survey\SurveyService;
use Illuminate\Http\JsonResponse;

class SurveyController extends BaseApiController
{
    private SurveyService $service;

    public function __construct(SurveyService $service)
    {
        $this->service = $service;
    }

    public function index(): JsonResponse
    {
        if (auth()->user()?->id === null) {
            return $this->respondOk([]);
        }

        return $this->respondOk($this->service->getManyByPagination(auth()->user()->id, 10));
    }

    public function show(int $id): JsonResponse
    {
        return $this->respondOk($this->service->getById($id));
    }

    public function store(CreateSurveyRequest $request): JsonResponse
    {
        $data = collect($request->all());
        $data->put('created_by', $request->user()->id);

        if ($request->origin !== null) {
            $data->put('version', $this->service->getSameRootSurveys($request->origin)->count());
        }

        $surveyId = $this->service->create($data->all());

        return $this->respondCreated($surveyId);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->service->delete($id);

        return $this->respondNoContent();
    }

    public function update(int $id, UpdateSurveyRequest $request): JsonResponse
    {
        $this->service->update($id, $request->all());

        return $this->respondNoContent();
    }

    public function copy(int $id): JsonResponse
    {
        $survey = $this->service->getById($id);

        $survey->created_by = auth()->user() === null ? $survey->created_by : auth()->user()->id;
        $survey->version = 0;
        $survey->origin = null;

        return $this->respondCreated($this->service->create($survey->toArray()));
    }
}
