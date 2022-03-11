<?php

namespace App\Services;

use App\Models\Unit;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UnitService
{
    public function getManyByPagination(int $perPage): LengthAwarePaginator
    {
        return Unit::paginate($perPage);
    }

    public function getById(int $id): Unit
    {
        return Unit::with('users')->findOrFail($id);
    }

    public function deleteById(int $id): bool
    {
        return Unit::findOrFail($id)->delete() === true;
    }

    public function create(array $data): int
    {
        return Unit::create($data)->id;
    }

    public function update(int $id, array $data): bool
    {
        return Unit::findOrFail($id)->update($data);
    }

    public function getUnitLevel(int $id): int
    {
        $level = 0;
        $node = Unit::findOrFail($id, ['parent_id']);
        while ($node->parent !== null) {
            $node = $node->parent;
            $level++;
        }

        return $level;
    }

    public function addUserToUnit(int $unitId, int $userId, int $type): bool
    {
        Unit::findOrFail($unitId)->users()->attach($userId, ['type' => $type]);

        return true;
    }
}
