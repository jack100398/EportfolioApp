<?php

namespace App\Http\Controllers\Workflow;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\Workflow\CreateIgnoreThresholdFormRequest;
use App\Http\Requests\Workflow\CreateIgnoreThresholdFormsRequest;
use App\Http\Requests\Workflow\IgnoreThresholdFormIndexRequest;
use App\Models\Auth\User;
use App\Services\Interfaces\IIgnoreThresholdFormService;
use App\Services\Interfaces\IScheduleSendWorkflowFormService;
use App\Services\Interfaces\IThresholdFormService;
use Illuminate\Http\JsonResponse;

class IgnoreThresholdFormController extends BaseApiController
{
    private IIgnoreThresholdFormService $service;

    private IThresholdFormService $thresholdService;

    private IScheduleSendWorkflowFormService $scheduleSendWorkflowFormService;

    public function __construct(
        IIgnoreThresholdFormService $ignoreThresholdFormService,
        IThresholdFormService $thresholdService,
        IScheduleSendWorkflowFormService $scheduleSendWorkflowFormService
    ) {
        $this->service = $ignoreThresholdFormService;
        $this->thresholdService = $thresholdService;
        $this->scheduleSendWorkflowFormService = $scheduleSendWorkflowFormService;
    }

    public function index(IgnoreThresholdFormIndexRequest $request): JsonResponse
    {
        return $this->respondOk($this->service->getByUserIdAndOriginThresholdIds($request->user_id, $request->origin_threshold_ids));
    }

    public function store(CreateIgnoreThresholdFormRequest $request): JsonResponse
    {
        $this->thresholdService->getById($request->origin_threshold_id);
        $ignoreThreshold = $this->service->store($request->all());
        $this->deleteScheduleForm($ignoreThreshold->origin_threshold_id, $ignoreThreshold->user_id);

        return $this->respondCreated($ignoreThreshold->id);
    }

    public function showUser(int $userId, int $originThresholdId): JsonResponse
    {
        return $this->respondOk($this->service->getByUserIdAndOriginThresholdId($userId, $originThresholdId));
    }

    public function stores(CreateIgnoreThresholdFormsRequest $request): JsonResponse
    {
        User::findOrFail($request->user_id);
        $thresholds = $this->thresholdService->getByIds($request->origin_threshold_ids);
        collect($thresholds)->map(function ($threshold) use ($request) {
            $ignoreThreshold = $this->service->store(
                [
                    'user_id' => $request->user_id,
                    'origin_threshold_id' => $threshold->id,
                ]
            );
            $this->deleteScheduleForm($ignoreThreshold->origin_threshold_id, $ignoreThreshold->user_id);
        });

        return $this->respondCreated(1);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->service->deleteById($id);
        //TODO:確認是否要新增學生表單到排程
        return $this->respondNoContent();
    }

    private function deleteScheduleForm(int $id, int $userId): void
    {
        $scheduleSendWorkflowForm = $this->scheduleSendWorkflowFormService->getQueueForm($id, $userId);
        if ($scheduleSendWorkflowForm !== null) {
            $this->scheduleSendWorkflowFormService->deleteById($scheduleSendWorkflowForm->id);
        }
    }
}
