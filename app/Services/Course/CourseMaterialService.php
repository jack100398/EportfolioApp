<?php

namespace App\Services\Course;

use App\Models\File;
use App\Models\Material\CourseMaterial;
use App\Models\Material\Material;

class CourseMaterialService
{
    public function create(array $data): int
    {
        return CourseMaterial::create($data)->id;
    }

    public function update(int $id, array $data): bool
    {
        return CourseMaterial::findOrFail($id)->update($data) === true;
    }

    public function deleteById(int $id): bool
    {
        return CourseMaterial::findOrFail($id)->delete() === true;
    }

    public function getById(int $id): CourseMaterial
    {
        return CourseMaterial::findOrFail($id);
    }
}
