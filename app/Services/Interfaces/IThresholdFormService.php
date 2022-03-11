<?php

namespace App\Services\Interfaces;

use App\Models\Workflow\ThresholdForm;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface IThresholdFormService
{
    public function getById(int $id): ThresholdForm;

    public function getByIds(array $ids): Collection;

    public function getManyByPagination(int $per_page): LengthAwarePaginator;

    public function getByProgramCategoryId(int $programCategoryId): Collection;

    public function updateThreshold(array $request, ThresholdForm $threshold): ThresholdForm;

    public function storeThreshold(array $request): ThresholdForm;
}
