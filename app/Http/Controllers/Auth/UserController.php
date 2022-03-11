<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Auth\AuthorizationAbility;
use App\Models\Auth\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * TODO:.職類設定.
 */
class UserController extends BaseApiController
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize(AuthorizationAbility::INDEX, User::class);

        $users = User::withTrashed()->latest();

        if (is_string($request->query('filter'))) {
            $users->where('name', 'LIKE', '%'.$request->query('filter').'%')
                ->orWhere('username', 'LIKE', '%'.$request->query('filter').'%');
        }

        return $this->respondOk($users->paginate(intval($request->query('size'))));
    }

    public function info(Request $request): JsonResponse
    {
        $this->authorize('info', $request->user());

        return $this->respondOk([
            'name' => $request->user()->name,
            'roles' => $request->user()->getRoleNames(),
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $user = User::withTrashed()->findOrFail($id);
        $this->authorize(AuthorizationAbility::SHOW, $user);

        $user['roles'] = $user->roles;

        return $this->respondOk($user);
    }

    public function store(CreateUserRequest $request): JsonResponse
    {
        $this->authorize(AuthorizationAbility::STORE, User::class);

        $request->merge(['password' => Hash::make($request->get('password'))]);

        $user = User::create($request->except('roles'));
        $user->syncRoles($request->input('roles'));

        return $this->respondCreated($user->id);
    }

    public function update(UpdateUserRequest $request, int $id): JsonResponse
    {
        $user = User::findOrFail($id);

        $this->authorize(AuthorizationAbility::UPDATE, $user);

        $user->fill($request->except('roles', 'permissions', 'password'));

        if ($request->get('password')) {
            $user->password = Hash::make($request->get('password'));
        }

        $user->syncRoles($request->input('roles.*.name'));

        $user->save();

        return $this->respondNoContent();
    }

    public function destroy(int $id): JsonResponse
    {
        $user = User::findOrFail($id);

        $this->authorize(AuthorizationAbility::DESTROY, $user);

        $user->delete();

        return $this->respondNoContent();
    }
}
