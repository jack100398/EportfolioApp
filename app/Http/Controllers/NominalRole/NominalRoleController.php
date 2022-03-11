<?php

namespace App\Http\Controllers\NominalRole;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\NominalRole\CreateNominalRoleRequest;
use App\Services\NominalRole\NominalRoleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NominalRoleController extends BaseApiController
{
    private NominalRoleService $service;

    public function __construct(NominalRoleService $service)
    {
        $this->service = $service;
    }

    public function index(): JsonResponse
    {
        $data = $this->service->getAll();

        return $this->respondOk($data);
    }

    public function store(CreateNominalRoleRequest $request): JsonResponse
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
