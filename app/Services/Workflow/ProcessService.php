<?php

namespace App\Services\Workflow;

use App\Models\Workflow\Process;
use App\Services\Interfaces\IProcessService;
use App\Services\Workflow\Enum\ProcessErrorStatusEnum;
use App\Services\Workflow\Enum\ProcessStateEnum;
use App\Services\Workflow\Enum\ProcessTypeEnum;
use Illuminate\Support\Collection;

class ProcessService implements IProcessService
{
    public function getNextProcess(int $next_process_id): Process
    {
        return Process::findOrFail($next_process_id);
    }

    public function getById(int $id): Process
    {
        return Process::findOrFail($id);
    }

    public function deleteById(int $id): bool
    {
        return Process::findOrFail($id)->delete() === true;
    }

    public function getByWorkflowId(int $workflowId): Collection
    {
        return Process::where('workflow_id', $workflowId)
            ->with('user')
            ->orderBy('next_process_id')->get();
    }

    public function getByErrorWorkflow(int $id, int $error_status): ?Process
    {
        return Process::where([
            ['id', $id],
            ['error_status', $error_status],
        ])->first();
    }

    public function getByDefaultProcess(int $workflowId): Collection
    {
        return Process::where([['is_default', true], ['workflow_id', $workflowId]])->orderBy('next_process_id')->get();
    }

    public function getByLastProcess(int $workflowId): ?Process
    {
        return Process::orderByDesc('id')->where('workflow_id', $workflowId)->first();
    }

    public function getProcessSequence(int $workflowId, array $processIds): Collection
    {
        return Process::orderBy('id')
            ->with('nextProcess')
            ->where('workflow_id', $workflowId)
            ->get()
            ->map(function ($value, $key) use ($processIds) {
                if (in_array($value->id, $processIds)) {
                    return $key;
                }
            })
            ->filter(function ($value) {
                return ! is_null($value);
            })
            ->sort();
    }

    public function getByBelowAllProcess(int $workflowId, int $skip, int $take): Collection
    {
        return Process::orderBy('id')
            ->with('nextProcess')
            ->where('workflow_id', $workflowId)
            ->skip($skip)
            ->take($take)
            ->get();
    }

    public function getNoStartProcess(int $signBy): Collection
    {
        return Process::where([
            ['state', ProcessStateEnum::NO_START],
            ['type', '<>', ProcessTypeEnum::EVALUATEE],
            ['sign_by', $signBy],
        ])->groupBy()->get();
    }

    public function getCanUpdateProcess(int $signBy, array $ids): Collection
    {
        return Process::where([
            ['state', ProcessStateEnum::NO_START],
            ['type', '<>', ProcessTypeEnum::EVALUATEE],
            ['sign_by', $signBy],
        ])->whereIn('id', $ids)->get();
    }

    public function updateReturnProcess(int $id, ?string $opinion): Process
    {
        $process = $this->getById($id);
        $process->state = ProcessStateEnum::RETURN;
        $process->opinion = $opinion ?? '';
        $process->update();

        return $process;
    }

    public function createDefaultProcess(int $oldId, int $newId): void
    {
        $defaultProcesses = $this->getByDefaultProcess($oldId);
        collect($defaultProcesses)->map(function ($defaultProcess) use ($newId) {
            $laseProcess = $this->getByLastProcess($newId);
            $newProcess = new Process();
            $newProcess->workflow_id = $newId;
            $newProcess->type = $defaultProcess->type;
            $newProcess->state = ProcessStateEnum::NO_START;
            $newProcess->role = $defaultProcess->role;
            $newProcess->opinion = '';
            //TODO:判斷是否有符合這個角色的人員
            $newProcess->sign_by = $defaultProcess->sign_by;
            $newProcess->error_status = ProcessErrorStatusEnum::NORMAL;
            $newProcess->save();
            if (! is_null($laseProcess)) {
                $laseProcess->next_process_id = $newProcess->id;
                $laseProcess->update();
            }
        });
    }

    public function connectNewBackProcess(Process $process, int $firstBackProcessId, int $lastBackProcessId, ?string $requestOpinion): void
    {
        $firstBackProcess = $this->getById((int) $firstBackProcessId);
        $lastBackProcess = $this->getById((int) $lastBackProcessId);
        $lastBackProcess->next_process_id = $process->next_process_id;
        $lastBackProcess->update();

        $this->updateProcessAgreeOrDisagree($process, ProcessStateEnum::DISAGREE, $requestOpinion, $firstBackProcess->id);
    }

    public function updateProcessAgreeOrDisagree(Process $process, int $state, ?string $requestOpinion = null, ?int $nextProcessId = null): void
    {
        $process->state = $state;
        if (! is_null($nextProcessId)) {
            $process->next_process_id = $nextProcessId;
        }
        $process->opinion = $requestOpinion ?? '';
        $process->update();
    }

    public function storeBackProcess(Collection $backProcesses, Process $process): Collection
    {
        return collect($backProcesses)->map(function ($backProcess, $key) use ($process) {
            return $this->addBackProcess($process->workflow_id, $backProcess, (int) $key)->id;
        });
    }

    public function getPreviousProcess(int $id): ?Process
    {
        return Process::where('next_process_id', $id)->first();
    }

    public function storeAddProcess(array $conditions, Process $previous_process): Process
    {
        $process = new Process();
        $process->workflow_id = $previous_process->workflow_id;
        $process->sign_by = $conditions['sign_by'];
        $process->type = $conditions['type'];
        $process->role = $conditions['role'];
        $process->opinion = '';
        $process->save();
        $previous_process->next_process_id = $process->id;
        $previous_process->update();

        return $process;
    }

    public function updateSignBy(Process $process, int $signBy): Process
    {
        $process->error_status = ProcessErrorStatusEnum::NORMAL;
        $process->sign_by = $signBy;
        $process->update();

        return $process;
    }

    public function updateRole(Process $process, int $role): Process
    {
        $process->role = $role;
        $process->update();

        return $process;
    }

    public function batchModifyProcessSignBy(Collection $processes, int $NewSignBy): Collection
    {
        return collect($processes)->map(function ($process) use ($NewSignBy) {
            return $this->updateSignBy($process, $NewSignBy)->id;
        });
    }

    private function addBackProcess(int $workflowId, Process $backProcess, int $key): Process
    {
        $laseProcess = $this->getByLastProcess($workflowId);
        $newProcess = new Process();
        $newProcess->workflow_id = $workflowId;
        $newProcess->type = $backProcess->type;
        $newProcess->state = ProcessStateEnum::NO_START;
        $newProcess->role = $backProcess->role;
        $newProcess->opinion = '';
        //TODO:判斷是否有符合這個角色的人員
        $newProcess->sign_by = $backProcess->sign_by;
        $newProcess->error_status = ProcessErrorStatusEnum::NORMAL;
        $newProcess->save();
        if (! is_null($laseProcess) && $key !== 0) {
            $laseProcess->next_process_id = $newProcess->id;
            $laseProcess->update();
        }

        return $newProcess;
    }
}
