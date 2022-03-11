<?php

namespace App\Http\Controllers\TrainingProgram;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\TrainingProgram\StoreStepTemplateRequest;
use App\Services\TrainingProgram\TrainingProgramStepTemplateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TrainingProgramStepTemplateController extends BaseApiController
{
    private TrainingProgramStepTemplateService $service;

    public function __construct(TrainingProgramStepTemplateService $service)
    {
        $this->service = $service;
    }

    public function show(int $id): JsonResponse
    {
        $exam = $this->service->getById($id);

        return $this->respondOk($exam);
    }

    public function store(StoreStepTemplateRequest $request): JsonResponse
    {
        $id = $this->service->create($request->all());

        return $this->respondCreated($id);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $this->service->update($id, $request->all());

        return $this->respondNoContent();
    }

    public function destroy(int $id): JsonResponse
    {
        $this->service->deleteById($id);

        return $this->respondNoContent();
    }

    public function getByTrainingProgramId(int $id): JsonResponse
    {
        $data = $this->service->getByTrainingProgramId($id);

        return $this->respondOk($data);
    }
}
