<?php

namespace App\Services\Exam;

use App\Models\Exam\ExamFolder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ExamFolderService
{
    public function getManyByPagination(int $perPage): LengthAwarePaginator
    {
        return ExamFolder::orderBy('created_at', 'DESC')
            ->paginate($perPage);
    }

    public function getById(int $id): ?ExamFolder
    {
        return ExamFolder::with('examQuestions')->Find($id);
    }

    public function deleteById(int $id): bool
    {
        return ExamFolder::findOrFail($id)->delete() === true;
    }

    public function create(array $data, int $createBy): int
    {
        $data['created_by'] = $createBy;

        return ExamFolder::create($data)->id;
    }

    public function update(int $id, array $data): bool
    {
        return ExamFolder::findOrFail($id)->update($data);
    }

    public function getFolderQuestions(int $id): Collection
    {
        return ExamFolder::findOrFail($id)->examQuestions;
    }

    public function giveAuthorizationToUser(int $id, int $userId): bool
    {
        ExamFolder::findOrFail($id)
            ->authUsers()->attach($userId);

        return true;
    }
}
