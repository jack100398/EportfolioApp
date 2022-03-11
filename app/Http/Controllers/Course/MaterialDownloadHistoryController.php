<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\Course\CreateMaterialDownloadHistoryRequest;
use App\Http\Requests\Course\UpdateMaterialDownloadHistoryRequest;
use App\Services\Course\MaterialDownloadHistoryService;
use Illuminate\Http\JsonResponse;

class MaterialDownloadHistoryController extends BaseApiController
{
    private MaterialDownloadHistoryService $service;

    public function __construct(MaterialDownloadHistoryService $service)
    {
        $this->service = $service;
    }

    public function index(): JsonResponse
    {
        return $this->respondOk($this->service->getManyByPagination(10));
    }

    public function store(CreateMaterialDownloadHistoryRequest $request): JsonResponse
    {
        $data = $request->collect();

        $data->put('student', $request->user()->id);

        $this->service->create($data->all());

        return $this->respondCreated(1);
    }

    public function update(int $id, UpdateMaterialDownloadHistoryRequest $request): JsonResponse
    {
        $this->service->update($id, $request->all());

        return $this->respondNoContent();
    }

    public function destroy(int $id): JsonResponse
    {
        $this->service->deleteById($id);

        return $this->respondNoContent();
    }

    public function show(int $id): JsonResponse
    {
        return $this->respondOk($this->service->getById($id));
    }
}
