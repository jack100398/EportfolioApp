<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddUserToUnitRequest;
use App\Http\Requests\StoreUnitRequest;
use App\Models\Unit;
use App\Services\UnitService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UnitController extends BaseApiController
{
    private UnitService $service;

    public function __construct(UnitService $service)
    {
        $this->service = $service;
    }

    public function index(): JsonResponse
    {
        $data = $this->service->getManyByPagination(10);

        return $this->respondOk($data);
    }

    public function getAll(): JsonResponse
    {
        return $this->respondOk(Unit::get());
    }

    public function show(int $id): JsonResponse
    {
        $unit = $this->service->getById($id);

        return $this->respondOk($unit);
    }

    public function store(StoreUnitRequest $request): JsonResponse
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

    public function addUser(AddUserToUnitRequest $request): JsonResponse
    {
        $this->service->addUserToUnit(
            $request->unit_id,
            $request->user_id,
            $request->type
        );

        return $this->respondNoContent();
    }
}
