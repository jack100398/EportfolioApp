<?php

namespace App\Http\Controllers\Workflow;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\Workflow\CreateThresholdFormRequest;
use App\Http\Requests\Workflow\ThresholdFormListRequest;
use App\Http\Requests\Workflow\UpdateThresholdFormRequest;
use App\Services\Form\Interfaces\IFormService;
use App\Services\Interfaces\IDefaultWorkflowService;
use App\Services\Interfaces\IThresholdFormService;
use Illuminate\Http\JsonResponse;

class ThresholdFormController extends BaseApiController
{
    private IThresholdFormService $thresholdService;

    private IFormService $formService;

    private IDefaultWorkflowService $defaultService;

    public function __construct(
        IThresholdFormService $thresholdFormService,
        IFormService $formService,
        IDefaultWorkflowService $defaultWorkflowService
    ) {
        $this->thresholdService = $thresholdFormService;
        $this->formService = $formService;
        $this->defaultService = $defaultWorkflowService;
    }

    public function index(ThresholdFormListRequest $request): JsonResponse
    {
        return $this->respondOk($this->thresholdService->getByProgramCategoryId($request->programCategoryId));
    }

    public function store(CreateThresholdFormRequest $request): JsonResponse
    {
        $this->formService->getById($request->form_id);
        $this->defaultService->getById($request->default_workflow_id);

        return $this->respondCreated($this->thresholdService->storeThreshold($request->all())->id);
    }

    public function show(int $id): JsonResponse
    {
        return $this->respondOk($this->thresholdService->getById($id));
    }

    public function update(UpdateThresholdFormRequest $request, int $id): JsonResponse
    {
        $threshold = $this->thresholdService->getById($id);
        $this->formService->getById($request->form_id);
        $this->defaultService->getById($request->default_workflow_id);
        //TODO:新增需要寄送的表單加入排程

        return $this->respondOk($this->thresholdService->updateThreshold($request->all(), $threshold));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->thresholdService->getById($id)->delete();

        return $this->respondNoContent();
    }
}
