<?php

namespace App\Http\Controllers\TrainingProgram;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\TrainingProgram\StoreProgramCategoryRequest;
use App\Services\TrainingProgram\TrainingProgramCategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TrainingProgramCategoryController extends BaseApiController
{
    private TrainingProgramCategoryService $service;

    public function __construct(TrainingProgramCategoryService $service)
    {
        $this->service = $service;
    }

    public function store(StoreProgramCategoryRequest $request): JsonResponse
    {
        $data = $request->collect()->put('created_by', $request->user()->id);
        $id = $this->service->create($data->all());

        return $this->respondCreated($id);
    }

    public function show(int $id): JsonResponse
    {
        $data = $this->service->getById($id);

        return $this->respondOk($data);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $this->service->update($id, $request->except('created_by'));

        return $this->respondNoContent();
    }

    public function destroy(int $id): JsonResponse
    {
        $this->service->deleteById($id);

        return $this->respondNoContent();
    }

    public function getByTrainingProgramId(int $programId, int $unitId): JsonResponse
    {
        if (auth()->user() === null) { // TODO: user check
            return $this->respondUnauthorized();
        }

        // TODO: 系統變數判斷要不要同步架構
        // $isUsingDefaultCategory = true;
        $userId = auth()->user()->id;
        $this->service->syncToDefaultCategories($programId, $unitId, $userId);
        $categories = $this->service->getByTrainingProgramAndUnitId($programId, $unitId);

        return $this->respondOk($categories);
    }
}
