<?php

namespace App\Services\TrainingProgram;

use App\Models\TrainingProgram\OccupationalClass;
use Illuminate\Support\Collection;

class OccupationalClassService
{
    public function create(array $data): int
    {
        return OccupationalClass::create($data)->id;
    }

    public function update(int $id, array $data): bool
    {
        return OccupationalClass::findOrFail($id)->update($data);
    }

    public function deleteById(int $id): bool
    {
        return OccupationalClass::findOrFail($id)->delete() === true;
    }

    public function getById(int $id): OccupationalClass
    {
        return OccupationalClass::with('children')->findOrFail($id);
    }

    public function getByParentId(?int $id): Collection
    {
        return OccupationalClass::with('children')->where('parent_id', $id)->get();
    }
}
