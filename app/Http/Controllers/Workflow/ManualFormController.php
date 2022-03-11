<?php

namespace App\Http\Controllers\Workflow;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\Workflow\CreateManualFormRequest;
use App\Services\Form\Interfaces\IFormService;
use App\Services\Interfaces\IDefaultWorkflowService;
use App\Services\Interfaces\IManualFormService;
use Illuminate\Http\JsonResponse;

class ManualFormController extends BaseApiController
{
    private IManualFormService $service;

    private IDefaultWorkflowService $defaultWorkflowService;

    private IFormService $formService;

    public function __construct(
        IManualFormService $manualFormService,
        IDefaultWorkflowService $defaultWorkflowService,
        IFormService $formService
    ) {
        $this->service = $manualFormService;
        $this->defaultWorkflowService = $defaultWorkflowService;
        $this->formService = $formService;
    }

    public function store(CreateManualFormRequest $request): JsonResponse
    {
        $this->defaultWorkflowService->getById($request->default_workflow_id);
        $this->formService->getById($request->form_id);

        $manualForm = $this->service->store($request->all());

        return $this->respondCreated($manualForm->id);
    }

    public function show(int $id): JsonResponse
    {
        return $this->respondOk($this->service->getById($id));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->service->deleteById($id);

        return $this->respondNoContent();
    }

    public function getByProgram(int $programId): JsonResponse
    {
        return $this->respondOk($this->service->getByProgramId($programId));
    }
}
