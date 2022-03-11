<?php

namespace App\Http\Controllers\Workflow;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\Workflow\ErrorWorkflowIndexRequest;
use App\Http\Requests\Workflow\ThresholdWorkflowRequest;
use App\Http\Requests\Workflow\WorkflowIndexListRequest;
use App\Services\Interfaces\IProcessService;
use App\Services\Interfaces\IWorkflowService;
use Illuminate\Http\JsonResponse;

class WorkflowController extends BaseApiController
{
    private IWorkflowService $workflowService;

    private IProcessService $processService;

    public function __construct(IWorkflowService $workflowService, IProcessService $processService)
    {
        $this->workflowService = $workflowService;

        $this->processService = $processService;
    }

    /**
     * 已發送表單查詢.
     */
    public function index(WorkflowIndexListRequest $request): JsonResponse
    {
        $workflows = $this->workflowService->getByWorkflowTypePagination($request->all());

        return $this->respondOk($this->workflowService->mapSendWorkflowList($workflows->items()));
    }

    /**
     * 異常表單查詢.
     */
    public function getByErrorIndex(ErrorWorkflowIndexRequest $request): JsonResponse
    {
        $workflows = $this->workflowService->getErrorWorkflowPagination($request->all());

        return $this->respondOk($this->workflowService->mapErrorWorkflowList($workflows->items()));
    }

    public function getThresholdWorkflow(ThresholdWorkflowRequest $request): JsonResponse
    {
        return $this->respondOk($this->workflowService->getThresholdFormWorkflowMany($request->thresholdFormIds));
    }

    public function show(int $id): JsonResponse
    {
        return $this->respondOk($this->workflowService->getById($id));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->workflowService->deleteById($id);

        return $this->respondNoContent();
    }

    /**
     * 顯示使用者可以批次修改的簽核.
     */
    public function getCanBatchModifyWorkflow(int $userId): JsonResponse
    {
        $workflowIds = $this->processService->getNoStartProcess($userId)
            ->pluck('workflow_id')->toArray();
        //TODO:show role
        return $this->respondOk($this->workflowService->getByIds($workflowIds));
    }
}
