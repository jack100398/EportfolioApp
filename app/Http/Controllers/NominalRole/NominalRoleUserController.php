<?php

namespace App\Http\Controllers\NominalRole;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\NominalRole\CreateNominalRoleUserRequest;
use App\Services\NominalRole\NominalRoleUserService;
use Illuminate\Http\JsonResponse;

class NominalRoleUserController extends BaseApiController
{
    private NominalRoleUserService $service;

    public function __construct(NominalRoleUserService $service)
    {
        $this->service = $service;
    }

    public function store(CreateNominalRoleUserRequest $request): JsonResponse
    {
        $data = $request->all();
        $id = $this->service->create($data['nominal_role_id'], $data['user_id'], $data['morph_id']);

        return $this->respondCreated($id);
    }

    public function show(int $id): JsonResponse
    {
        $data = $this->service->getById($id);

        return $this->respondOk($data);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->service->deleteById($id);

        return $this->respondNoContent();
    }
}
