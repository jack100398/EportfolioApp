<?php

namespace App\Http\Controllers\TrainingProgram;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\TrainingProgram\StoreProgramStepRequest;
use App\Services\TrainingProgram\TrainingProgramStepService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TrainingProgramStepController extends BaseApiController
{
    private TrainingProgramStepService $service;

    public function __construct(TrainingProgramStepService $service)
    {
        $this->service = $service;
    }

    public function show(int $id): JsonResponse
    {
        $exam = $this->service->getById($id);

        return $this->respondOk($exam);
    }

    public function store(StoreProgramStepRequest $request): JsonResponse
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

    public function userSteps(int $userId): JsonResponse
    {
        $data = $this->service->getUserSteps($userId);

        return $this->respondOk($data);
    }
}
