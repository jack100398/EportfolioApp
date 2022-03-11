<?php

namespace App\Services\Course;

use App\Models\Auth\User;
use App\Models\Material\Material;
use App\Models\Material\MaterialAuthorize;
use Illuminate\Database\Eloquent\Builder;

class MaterialService
{
    public function getOwnMaterials(bool $isFolder, int $folderId, Builder $materials): Builder
    {
        if ($isFolder) {
            $materials->where('folder_id', $folderId);
        } else {
            $materials->whereNull('folder_id');
        }

        return $materials;
    }

    public function getAuthorizedMaterials(int $userId, Builder $materials): Builder
    {
        $authorizeMaterials = MaterialAuthorize::where('authorize_type', User::class)
        ->where('authorize_id', $userId)
        ->pluck('material_id');

        $materials->whereIn('materials.id', $authorizeMaterials);

        return $materials;
    }

    public function create(array $data): int
    {
        return Material::create($data)->id;
    }

    public function update(int $id, array $data): bool
    {
        return Material::findOrFail($id)->update($data) === true;
    }

    public function deleteMaterialById(int $id): bool
    {
        return Material::findOrFail($id)->delete() === true;
    }

    public function getMaterialById(int $id): Material
    {
        return Material::findOrFail($id);
    }

    public function authUser(int $id, int $targetId): void
    {
        $userId = User::findOrFail($targetId)->id;
        Material::findOrFail($id)
            ->authUser()
            ->attach($userId);
    }

    public function authUnit(int $id, int $targetId): void
    {
        Material::findOrFail($id)
            ->authUnit()
            ->attach($targetId);
    }

    public function deAuthUser(int $id, int $targetId): void
    {
        $userId = User::findOrFail($targetId)->id;
        Material::findOrFail($id)
            ->authUser()
            ->detach($userId);
    }

    public function deAuthUnit(int $id, int $targetId): void
    {
        Material::findOrFail($id)
            ->authUnit()
            ->detach($targetId);
    }
}
