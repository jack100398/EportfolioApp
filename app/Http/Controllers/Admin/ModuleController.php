<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseApiController;
use App\Models\Admin\Module;
use Illuminate\Http\JsonResponse;

class ModuleController extends BaseApiController
{
    public function index(): JsonResponse
    {
        return $this->respondOk(Module::latest()->paginate());
    }

    // public function show(int $id): JsonResponse
    // {
    //     $user = User::findOrFail($id);
    //     $this->authorize(AuthorizationAbility::SHOW, $user);

    //     return $this->respondOk($user);
    // }

    // public function store(CreateUserRequest $request): JsonResponse
    // {
    //     $this->authorize(AuthorizationAbility::STORE, User::class);

    //     $request->merge(['password' => Hash::make($request->get('password'))]);

    //     $user = User::create($request->except('roles'));
    //     $user->syncRoles($request->input('roles'));

    //     return $this->respondCreated($user->id);
    // }

    // public function update(UpdateUserRequest $request, int $id): JsonResponse
    // {
    //     $user = User::findOrFail($id);

    //     $this->authorize(AuthorizationAbility::UPDATE, $user);

    //     $user->fill($request->except('roles', 'permissions', 'password'));

    //     if ($request->get('password')) {
    //         $user->password = Hash::make($request->get('password'));
    //     }

    //     $user->syncRoles($request->input('roles'));

    //     $user->save();

    //     return $this->respondNoContent();
    // }

    // public function destroy(int $id): JsonResponse
    // {
    //     $user = User::findOrFail($id);

    //     $this->authorize(AuthorizationAbility::DESTROY, $user);

    //     $user->delete();

    //     return $this->respondNoContent();
    // }
}
