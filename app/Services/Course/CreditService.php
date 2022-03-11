<?php

namespace App\Services\Course;

use App\Models\Course\Credit;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class CreditService
{
    public function getManyByPagination(int $perPage): LengthAwarePaginator
    {
        return Credit::orderBy('created_at', 'DESC')
            ->paginate($perPage);
    }

    public function create(array $data): int
    {
        return Credit::create($data)->id;
    }

    public function update(int $id, array $data): bool
    {
        return Credit::findOrFail($id)->update($data) === true;
    }

    public function deleteCreditById(int $id): bool
    {
        return Credit::findOrFail($id)->delete() === true;
    }

    public function getCreditById(int $id): Credit
    {
        return Credit::findOrFail($id);
    }

    public function getHospitalCredits(): Collection
    {
        return Credit::where('credit_type', 1)->get();
    }

    public function getContinueCredits(int $parentId): Collection
    {
        $query = Credit::where('credit_type', 2);

        if ($parentId !== 0) {
            $query->where('parent_id', $parentId);
        } else {
            $query->whereNull('parent_id');
        }

        return $query->get();
    }

    public function getByYear(int $year): Collection
    {
        return Credit::where('year', $year)->get();
    }
}
