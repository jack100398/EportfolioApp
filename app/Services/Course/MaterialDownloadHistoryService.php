<?php

namespace App\Services\Course;

use App\Models\Material\MaterialDownloadHistory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class MaterialDownloadHistoryService
{
    public function getManyByPagination(int $perPage): LengthAwarePaginator
    {
        return MaterialDownloadHistory::orderBy('created_at', 'DESC')
            ->paginate($perPage);
    }

    public function create(array $data): int
    {
        return MaterialDownloadHistory::create($data)->id;
    }

    public function update(int $id, array $data): bool
    {
        return MaterialDownloadHistory::findOrFail($id)->update($data) === true;
    }

    public function deleteById(int $id): bool
    {
        return MaterialDownloadHistory::findOrFail($id)->delete() === true;
    }

    public function getById(int $id): MaterialDownloadHistory
    {
        return MaterialDownloadHistory::findOrFail($id);
    }
}
