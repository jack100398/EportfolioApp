<?php

namespace App\Http\Controllers\Workflow;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\Workflow\AddAssignProcessRequest;
use App\Http\Requests\Workflow\DisagreeProcessRequest;
use App\Http\Requests\Workflow\ProcessIndexRequest;
use App\Http\Requests\Workflow\ReturnWorkflowRequest;
use App\Http\Requests\Workflow\UpdateBatchModifyProcessRequest;
use App\Http\Requests\Workflow\UpdateProcessRequest;
use App\Http\Requests\Workflow\UpdateWorkflowRoleRequest;
use App\Http\Requests\Workflow\UpdateWorkflowSignByRequest;
use App\Models\Auth\User;
use App\Services\Interfaces\IProcessService;
use App\Services\Interfaces\IWorkflowService;
use App\Services\NominalRole\NominalRoleService;
use App\Services\Workflow\Enum\ProcessErrorStatusEnum;
use App\Services\Workflow\Enum\ProcessStateEnum;
use App\Services\Workflow\Enum\ProcessTypeEnum;
use Illuminate\Http\JsonResponse;

class ProcessController extends BaseApiController
{
    private IProcessService $processService;

    private IWorkflowService $workflowService;

    private NominalRoleService $nominalRoleService;

    public function __construct(
        IProcessService $processService,
        IWorkflowService $workflowService,
        NominalRoleService $nominalRoleService
    ) {
        $this->processService = $processService;
        $this->workflowService = $workflowService;
        $this->nominalRoleService = $nominalRoleService;
    }

    /**
     * 簽核頁面預覽流程列表狀態.
     */
    public function index(ProcessIndexRequest $request): JsonResponse
    {
        $getByWorkflowId = $this->processService->getByWorkflowId($request->workflow_id);
        $processes = collect($getByWorkflowId)->map(function ($process) {
            if ($process->type === ProcessTypeEnum::ANONYMOUS) {
                $process->name = '匿名填寫';
            }

            return $process;
        });

        return $this->respondOk($processes);
    }

    /**
     * 加簽.
     */
    public function store(AddAssignProcessRequest $request): JsonResponse
    {
        $this->nominalRoleService->getById($request->role);
        $lastProcess = $this->processService->getById($request->process_id);
        $signBy = User::find($request->sign_by);
        if (is_null($signBy)) {
            return $this->respondNotFound();
        }
        $process = $this->processService->storeAddProcess($request->all(), $lastProcess);
        //TODO::通知下一位簽核者
        return $this->respondCreated($process->id);
    }

    /*
     * 退件.
     */
    public function returnWorkflow(ReturnWorkflowRequest $request, int $id): JsonResponse
    {
        $process = $this->processService->updateReturnProcess($id, $request->opinion);
        $returnWorkflow = $this->workflowService->updateReturnWorkflow($process->workflow_id);
        $newWorkflow = $this->workflowService->replaceReturnWorkflow($returnWorkflow);
        $this->processService->createDefaultProcess($process->workflow_id, $newWorkflow->id);
        //TODO:寄送通知給第一位簽核者
        return $this->respondOk($newWorkflow);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->processService->deleteById($id);

        return $this->respondNoContent();
    }

    /**
     * 不同意 ,退回某一層,並插入從退回那層開始到最後一層.
     */
    public function disagree(int $workflowId, DisagreeProcessRequest $request): JsonResponse
    {
        $process = $this->processService->getById($request->process_id);
        $sequences = $this->processService->getProcessSequence($workflowId, $request->all());
        $backProcesses = $this->processService->getByBelowAllProcess($workflowId, (int) $sequences->first(), (int) ($sequences->last() - $sequences->first() + 1));
        $backProcessIds = $this->processService->storeBackProcess($backProcesses, $process);

        $this->processService->connectNewBackProcess(
            $process,
            (int) $backProcessIds->first(),
            (int) $backProcessIds->last(),
            $request->opinion
        );

        //TODO:通知下一位簽核者
        return $this->respondOk($process);
    }

    /**
     * 同意.
     */
    public function update(UpdateProcessRequest $request, int $id): JsonResponse
    {
        $process = $this->processService->getById($id);
        $this->processService->updateProcessAgreeOrDisagree($process, ProcessStateEnum::AGREE);
        //TODO:需儲存表單資料

        //TODO:通知下一位簽核者
        return $this->respondOk($process);
    }

    /**
     * 更換簽核者.
     */
    public function updateSignBy(UpdateWorkflowSignByRequest $request, int $id): JsonResponse
    {
        $process = $this->processService->getByErrorWorkflow($id, ProcessErrorStatusEnum::NOT_FOUND_EVALUATEE);
        $signBy = User::find($request->sign_by);
        if (is_null($process) || is_null($signBy)) {
            return $this->respondNotFound();
        }

        return $this->respondOk($this->processService->updateSignBy($process, $request->sign_by));
    }

    /**
     * 更換角色.
     */
    public function updateRole(UpdateWorkflowRoleRequest $request, int $id): JsonResponse
    {
        $process = $this->processService->getByErrorWorkflow($id, ProcessErrorStatusEnum::NO_SETTING_ROLE);
        $this->nominalRoleService->getById($request->role);
        if (is_null($process)) {
            return $this->respondNotFound();
        }
        $process->error_status = ProcessErrorStatusEnum::NORMAL;

        return $this->respondOk($this->processService->updateRole($process, $request->role));
    }

    /**
     * 批次修改簽核者.
     */
    public function updateBatchModifyProcess(UpdateBatchModifyProcessRequest $request): JsonResponse
    {
        $processes = $this->processService->getCanUpdateProcess($request->old_sign_by, $request->ids);

        return $this->respondOk($this->processService
            ->batchModifyProcessSignBy($processes, $request->new_sign_by));
    }
}
