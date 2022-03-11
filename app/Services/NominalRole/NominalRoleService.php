<?php

namespace App\Services\NominalRole;

use App\Models\NominalRole\NominalRole;
use Illuminate\Support\Collection;

class NominalRoleService
{
    public function getAll(): Collection
    {
        return NominalRole::all();
    }

    public function create(array $data): int
    {
        return NominalRole::create($data)->id;
    }

    public function update(int $id, array $data): bool
    {
        return NominalRole::findOrFail($id)->update($data);
    }

    public function deleteById(int $id): bool
    {
        return NominalRole::findOrFail($id)->delete() === true;
    }

    public function getById(int $id): NominalRole
    {
        return NominalRole::findOrFail($id);
    }
}
