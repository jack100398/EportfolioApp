<?php

namespace App\Services\NominalRole;

use App\Models\NominalRole\NominalRole;
use App\Models\NominalRole\NominalRoleUser;
use Illuminate\Support\Collection;

class NominalRoleUserService
{
    public function create(int $nominalRoleId, int $userId, int $morphId): int
    {
        $nominalRole = NominalRole::findOrFail($nominalRoleId);
        $roleModel = NominalRole::TYPES[$nominalRole->type];

        $roleModel::findOrFail($morphId);

        return NominalRoleUser::create([
            'user_id' => $userId,
            'nominal_role_id' => $nominalRoleId,
            'roleable_type' => $roleModel,
            'roleable_id' => $morphId,
        ])->id;
    }

    public function deleteById(int $id): bool
    {
        return NominalRoleUser::findOrFail($id)->delete() === true;
    }

    public function getById(int $id): NominalRoleUser
    {
        return NominalRoleUser::findOrFail($id);
    }

    public function cloneNominalRoleUsers(Collection $roleUsers, int $roleableId): Collection
    {
        return $roleUsers->map(
            function (NominalRoleUser $roleUser) use ($roleableId) {
                $newRoleUser = $roleUser->replicate();
                $newRoleUser->roleable_id = $roleableId;
                $newRoleUser->save();

                return $newRoleUser;
            }
        );
    }
}
