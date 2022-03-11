<?php

namespace App\Http\Controllers\Workflow;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\Workflow\CreateDefaultWorkflowRequest;
use App\Http\Requests\Workflow\DefaultWorkflowListRequest;
use App\Http\Requests\Workflow\UpdateDefaultWorkflowRequest;
use App\Services\Interfaces\IDefaultWorkflowService;
use Illuminate\Http\JsonResponse;

class DefaultWorkflowController extends BaseApiController
{
    private IDefaultWorkflowService $service;

    public function __construct(IDefaultWorkflowService $service)
    {
        $this->service = $service;
    }

    public function index(DefaultWorkflowListRequest $request): JsonResponse
    {
        return $this->respondOk($this->service->getByPagination($request->all()));
    }

    public function store(CreateDefaultWorkflowRequest $request): JsonResponse
    {
        return $this->respondCreated($this->service->store($request->all())->id);
    }

    public function show(int $id): JsonResponse
    {
        return $this->respondOk($this->service->getById($id));
    }

    public function update(UpdateDefaultWorkflowRequest $request, int $id): JsonResponse
    {
        $defaultWorkflow = $this->service->getById($id);
        $this->service->update($defaultWorkflow, $request->all());

        return $this->respondNoContent();
    }

    public function destroy(int $id): JsonResponse
    {
        $this->service->deleteById($id);

        return $this->respondNoContent();
    }
}
