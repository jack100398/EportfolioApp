<?php

namespace App\Services\Interfaces;

use App\Models\Workflow\DefaultWorkflow;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface IDefaultWorkflowService
{
    public function getByPagination(array $condition): LengthAwarePaginator;

    public function getById(int $id): DefaultWorkflow;

    public function getByIds(array $ids): Collection;

    public function checkDefaultWorkflow(array $defaultWorkflows): bool;

    public function update(DefaultWorkflow $defaultWorkflow, array $data): bool;

    public function store(array $conditions): DefaultWorkflow;

    public function deleteById(int $id): bool;
}
