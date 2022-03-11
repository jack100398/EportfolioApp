<?php

namespace App\Http\Controllers\TrainingProgram;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\TrainingProgram\StoreAttachmentRequest;
use App\Services\TrainingProgram\TrainingProgramAttachmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TrainingProgramAttachmentController extends BaseApiController
{
    private TrainingProgramAttachmentService $service;

    public function __construct(TrainingProgramAttachmentService $service)
    {
        $this->service = $service;
    }

    public function show(int $id): JsonResponse
    {
        $attachment = $this->service->getById($id);

        return $this->respondOk($attachment);
    }

    public function store(StoreAttachmentRequest $request): JsonResponse
    {
        $id = $this->service->create($request->all());

        return $this->respondCreated($id);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $this->service->update($id, $request->all());

        return $this->respondNoContent();
    }

    public function destroy(int $id): JsonResponse
    {
        $this->service->deleteById($id);

        return $this->respondNoContent();
    }

    public function getByTrainingProgramId(int $id): JsonResponse
    {
        $data = $this->service->getByTrainingProgramId($id);

        return $this->respondOk($data);
    }
}
