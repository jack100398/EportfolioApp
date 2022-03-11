<?php

namespace App\Services\Interfaces;

use App\Models\Workflow\Process;
use Illuminate\Support\Collection;

interface IProcessService
{
    public function getNextProcess(int $next_process_id): ?Process;

    public function getById(int $id): Process;

    public function deleteById(int $id): bool;

    public function getByWorkflowId(int $workflowId): Collection;

    public function getByErrorWorkflow(int $id, int $error_status): ?Process;

    public function getByDefaultProcess(int $workflowId): Collection;

    public function getByLastProcess(int $workflowId): ?Process;

    /**
     * 關卡的順序位置.
     *
     * @param array $processIds 篩選要顯示的關卡順序位置
     */
    public function getProcessSequence(int $workflowId, array $processIds): Collection;

    /**
     * 獲得簽核流程要退回的關卡到目前這關卡的項目.
     *
     * @param int $skip 退回關卡的順序位置
     * @param int $take 目前這關卡的順序位置
     */
    public function getByBelowAllProcess(int $workflowId, int $skip, int $take): Collection;

    public function getNoStartProcess(int $signBy): Collection;

    public function getCanUpdateProcess(int $signBy, array $ids): Collection;

    public function updateReturnProcess(int $id, ?string $opinion): Process;

    public function createDefaultProcess(int $oldId, int $newId): void;

    public function connectNewBackProcess(Process $process, int $firstBackProcessId, int $lastBackProcessId, ?string $requestOpinion): void;

    public function updateProcessAgreeOrDisagree(Process $process, int $state, ?string $requestOpinion = null, ?int $nextProcessId = null): void;

    public function getPreviousProcess(int $id): ?Process;

    public function storeAddProcess(array $conditions, Process $previous_process): Process;

    public function storeBackProcess(Collection $backProcesses, Process $process): Collection;

    public function updateSignBy(Process $process, int $signBy): Process;

    public function updateRole(Process $process, int $role): Process;

    /**
     * 批次修改流程簽核者.
     */
    public function batchModifyProcessSignBy(Collection $processes, int $NewSignBy): Collection;
}
