<?php

namespace App\Http\Controllers\TrainingProgram;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\TrainingProgram\StoreProgramUserRequest;
use App\Services\TrainingProgram\TrainingProgramUserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TrainingProgramUserController extends BaseApiController
{
    private TrainingProgramUserService $service;

    public function __construct(TrainingProgramUserService $service)
    {
        $this->service = $service;
    }

    public function show(int $id): JsonResponse
    {
        $exam = $this->service->getById($id);

        return $this->respondOk($exam);
    }

    public function store(StoreProgramUserRequest $request): JsonResponse
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
}
