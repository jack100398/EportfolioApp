<?php

namespace App\Services\Course\Survey;

use App\Models\Course\Survey\Survey;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class SurveyService
{
    public function getManyByPagination(int $owner, int $perPage): LengthAwarePaginator
    {
        return Survey::where('created_by', $owner)
            ->orderBy('created_at', 'DESC')
            ->paginate($perPage);
    }

    public function getById(int $id): Survey
    {
        return Survey::findOrFail($id);
    }

    public function update(int $id, array $data): bool
    {
        return Survey::findOrFail($id)->update($data) === true;
    }

    public function create(array $data): int
    {
        return Survey::create($data)->id;
    }

    public function delete(int $id): bool
    {
        return Survey::findOrFail($id)->delete() === true;
    }

    public function getSameRootSurveys(int $id): Collection
    {
        $surveys = Survey::where('origin', $id)->get();

        $surveys->push(Survey::findOrFail($id));

        return $surveys;
    }
}
