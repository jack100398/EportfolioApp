<?php

namespace App\Services\Interfaces;

use App\Models\Workflow\Workflow;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface IWorkflowService
{
    public function getById(int $id): Workflow;

    public function getByDataId(int $dataId, int $workflowTypeEnum): Collection;

    public function deleteById(int $id): bool;

    public function getByIdWithProcess(int $id, int $workflowTypeEnum): Collection;

    public function getThresholdFormWorkflowMany(array $thresholdFormIds): Collection;

    public function getByWorkflowTypePagination(array $conditions): LengthAwarePaginator;

    public function getErrorWorkflowPagination(array $conditions): LengthAwarePaginator;

    public function getWorkflowLateUser(int $id): ?Workflow;

    public function getByIds(array $ids): Collection;

    public function updateReturnWorkflow(int $id): Workflow;

    public function replaceReturnWorkflow(Workflow $returnWorkflow): Workflow;

    public function mapSendWorkflowList(array $workflows): Collection;

    public function mapErrorWorkflowList(array $workflows): Collection;

    public function getByIdWithForm(int $id): ?Workflow;
}
