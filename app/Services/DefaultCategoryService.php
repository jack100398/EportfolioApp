<?php

namespace App\Services;

use App\Models\DefaultCategory;
use Illuminate\Support\Collection;

class DefaultCategoryService
{
    public function getAll(): Collection
    {
        return DefaultCategory::all();
    }

    public function create(array $data): int
    {
        return DefaultCategory::create($data)->id;
    }

    public function update(int $id, array $data): bool
    {
        return DefaultCategory::findOrFail($id)->update($data);
    }

    public function deleteById(int $id): bool
    {
        return DefaultCategory::findOrFail($id)->delete() === true;
    }

    public function getById(int $id): DefaultCategory
    {
        return DefaultCategory::findOrFail($id);
    }
}
