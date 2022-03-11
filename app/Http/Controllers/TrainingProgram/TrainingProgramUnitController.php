<?php

namespace App\Http\Controllers\TrainingProgram;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\TrainingProgram\StoreProgramUnitRequest;
use App\Services\TrainingProgram\TrainingProgramUnitService;
use Illuminate\Http\JsonResponse;

class TrainingProgramUnitController extends BaseApiController
{
    private TrainingProgramUnitService $service;

    public function __construct(TrainingProgramUnitService $service)
    {
        $this->service = $service;
    }

    public function show(int $id): JsonResponse
    {
        $exam = $this->service->getById($id);

        return $this->respondOk($exam);
    }

    public function store(StoreProgramUnitRequest $request): JsonResponse
    {
        $id = $this->service->create($request->all());

        return $this->respondCreated($id);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->service->deleteById($id);

        return $this->respondNoContent();
    }
}
