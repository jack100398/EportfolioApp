<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\CreateRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Models\Auth\AuthorizationAbility;
use App\Models\Auth\Role;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class RoleController extends BaseApiController
{
    public function index(): JsonResponse
    {
        return $this->respondOk(Role::latest()->paginate());
    }

    public function store(CreateRoleRequest $request): JsonResponse
    {
        $this->authorize(AuthorizationAbility::STORE, Role::class);

        $role = Role::create(
            [
                'name' => $request->input('name'),
                'readable_name' => $request->input('readable_name'),
                'description' => $request->input('description'),
                'guard_name' => 'api',
            ]
        );
        $role->syncPermissions($request->input('permissions'));

        return $this->respondCreated($role->id);
    }

    public function show(int $id): JsonResponse
    {
        return $this->respondOk(Role::findOrFail($id));
    }

    public function update(UpdateRoleRequest $request, int $id): Response
    {
        $role = Role::findOrFail($id);

        $this->authorize(AuthorizationAbility::UPDATE, $role);

        $role->fill([
            'name' => $request->input('name'),
            'readable_name' => $request->input('readable_name'),
            'description' => $request->input('description'),
        ]);

        $role->syncPermissions($request->input('permissions'));

        $role->save();

        return $this->respondNoContent();
    }

    public function destroy(int $id): JsonResponse
    {
        Role::destroy($id);

        return $this->respondNoContent();
    }
}
