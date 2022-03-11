<?php

namespace App\Http\Controllers\Course\Survey;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\Course\Survey\CreateSurveyQuestionRequest;
use App\Http\Requests\Course\Survey\UpdateSurveyQuestionRequest;
use App\Services\Course\Survey\SurveyQuestionService;
use Illuminate\Http\JsonResponse;

class SurveyQuestionController extends BaseApiController
{
    private SurveyQuestionService $service;

    public function __construct(SurveyQuestionService $service)
    {
        $this->service = $service;
    }

    public function show(int $id): JsonResponse
    {
        return $this->respondOk($this->service->getById($id));
    }

    public function store(CreateSurveyQuestionRequest $request): JsonResponse
    {
        $data = collect($request->all());

        $data->put('metadata', ['content' => $request->option_content, 'score' => $request->option_score]);

        return $this->respondCreated($this->service->create($data->all()));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->service->delete($id);

        return $this->respondNoContent();
    }

    public function update(int $id, UpdateSurveyQuestionRequest $request): JsonResponse
    {
        $data = collect($request->all());

        $data->put('metadata', [$request->option_content, $request->option_score]);

        $this->service->update($id, $request->all());

        return $this->respondNoContent();
    }
}
