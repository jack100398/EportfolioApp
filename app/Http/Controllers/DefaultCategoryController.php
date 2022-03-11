<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDefaultCategoryRequest;
use App\Services\DefaultCategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DefaultCategoryController extends BaseApiController
{
    private DefaultCategoryService $service;

    public function __construct(DefaultCategoryService $service)
    {
        $this->service = $service;
    }

    public function index(): JsonResponse
    {
        $data = $this->service->getAll();

        return $this->respondOk($data);
    }

    public function store(StoreDefaultCategoryRequest $request): JsonResponse
    {
        $id = $this->service->create($request->all());

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
}
