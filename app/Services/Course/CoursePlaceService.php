<?php

namespace App\Services\Course;

use App\Models\Course\CoursePlace;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class CoursePlaceService
{
    public function getManyByPagination(int $perPage): LengthAwarePaginator
    {
        return CoursePlace::orderBy('id', 'DESC')
            ->paginate($perPage);
    }

    public function getPlaceByParentId(int $parentId): Collection
    {
        $query = CoursePlace::orderBy('id', 'ASC');
        if ($parentId > 0) {
            $query->where('parent_id', $parentId);
        } else {
            $query->whereNull('parent_id');
        }

        return $query->get();
    }

    public function getById(int $id): CoursePlace
    {
        return CoursePlace::findOrFail($id);
    }

    public function create(array $data): int
    {
        return CoursePlace::create($data)->id;
    }

    public function delete(int $id): bool
    {
        $place = $this->getById($id);

        return $place->delete() === true;
    }

    public function update(int $id, array $data): bool
    {
        $place = $this->getById($id);

        return $place->update($data) === true;
    }
}
