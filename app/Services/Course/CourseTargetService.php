<?php

namespace App\Services\Course;

use App\Models\Course\CourseTarget;
use Illuminate\Database\Eloquent\Collection;

class CourseTargetService
{
    public function create(array $courseTarget): int
    {
        return CourseTarget::create($courseTarget)->id;
    }

    public function update(int $id, array $data): bool
    {
        return CourseTarget::findOrFail($id)->update($data) === true;
    }

    public function deleteById(int $id): bool
    {
        return CourseTarget::findOrFail($id)->delete() === true;
    }

    public function getById(int $Id): ?CourseTarget
    {
        return CourseTarget::findOrFail($Id);
    }

    public function getAll(): Collection
    {
        return CourseTarget::get();
    }
}
