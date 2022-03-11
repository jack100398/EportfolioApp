<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\PermissionRequest;
use App\Models\Admin\Module;
use App\Models\Auth\Permission;
use App\Models\Auth\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class PermissionController extends BaseApiController
{
    public function index(): JsonResponse
    {
        $modulePermissions = Module::where('is_enabled', '=', true)
            ->get()
            ->map(function (Module $module) {
                return [
                    'name' => $module->name,
                    'unique_name' => $module->unique_name,
                    'view' => false,
                    'add' => false,
                    'edit' => false,
                    'delete' => false,
                ];
            });

        return $this->respondOk($modulePermissions);
    }

    public function showUserPermissions(int $roleId): JsonResponse
    {
        $permissions = Role::findOrFail($roleId)->getAllPermissions();

        $modulePermissions = Module::where('is_enabled', '=', true)
            ->whereNotNull('unique_name')
            ->get(['unique_name', 'name'])
            ->map(function ($module) use ($permissions) {
                return [
                    'name' => $module->name,
                    'unique_name' => $module->unique_name,
                    'view' => $this->checkHasPermission($permissions, Permission::VIEW.$module->unique_name),
                    'add' => $this->checkHasPermission($permissions, Permission::ADD.$module->unique_name),
                    'edit' => $this->checkHasPermission($permissions, Permission::EDIT.$module->unique_name),
                    'delete' => $this->checkHasPermission($permissions, Permission::DELETE.$module->unique_name),
                ];
            });

        return $this->respondOk($modulePermissions);
    }

    public function updateUserPermissions(Request $request, int $roleId): JsonResponse
    {
        $permissions = collect($request->json())
            ->map(function (array $permission) {
                $arr = [];

                if ($permission['view'] === true) {
                    array_push($arr, Permission::VIEW.$permission['unique_name']);
                }
                if ($permission['add'] === true) {
                    array_push($arr, Permission::ADD.$permission['unique_name']);
                }
                if ($permission['edit'] === true) {
                    array_push($arr, Permission::EDIT.$permission['unique_name']);
                }
                if ($permission['delete'] === true) {
                    array_push($arr, Permission::DELETE.$permission['unique_name']);
                }

                return $arr;
            })->flatten()->toArray();

        Role::findOrFail($roleId)->syncPermissions($permissions);

        return $this->respondNoContent();
    }

    public function store(PermissionRequest $request): JsonResponse
    {
        $permission = Permission::create($request->only('name'));

        return $this->respondCreated($permission->id);
    }

    public function show(int $id): JsonResponse
    {
        return $this->respondOk(Permission::findById($id));
    }

    public function update(PermissionRequest $request, int $id): JsonResponse
    {
        $permission = Permission::findOrFail($id);
        $permission->fill(['name' => $request->input('name')]);
        $permission->save();

        return $this->respondNoContent();
    }

    public function destroy(int $id): JsonResponse
    {
        Permission::destroy($id);

        return $this->respondNoContent();
    }

    private function checkHasPermission(Collection $permissions, string $permissionName): bool
    {
        return $permissions
            ->filter(function ($permission) use ($permissionName) {
                return str_contains($permission->name, $permissionName);
            })
            ->count() > 0;
    }
}
