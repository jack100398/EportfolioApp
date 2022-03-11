<?php

namespace App\Http\Controllers\TrainingProgram;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\TrainingProgram\StoreOccupationalClassRequest;
use App\Services\TrainingProgram\OccupationalClassService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OccupationalClassController extends BaseApiController
{
    private OccupationalClassService $service;

    public function __construct(OccupationalClassService $service)
    {
        $this->service = $service;
    }

    public function index(): JsonResponse
    {
        // 第一層為職類，二層為計劃分類
        $data = $this->service->getByParentId(null);

        return $this->respondOk($data);
    }

    public function show(int $id): JsonResponse
    {
        $data = $this->service->getById($id);

        return $this->respondOk($data);
    }

    public function store(StoreOccupationalClassRequest $request): JsonResponse
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
