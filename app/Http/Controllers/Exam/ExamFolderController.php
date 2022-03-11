<?php

namespace App\Http\Controllers\Exam;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\Exam\StoreExamFolderRequest;
use App\Services\Exam\ExamFolderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExamFolderController extends BaseApiController
{
    private ExamFolderService $service;

    public function __construct(ExamFolderService $service)
    {
        $this->service = $service;
    }

    public function index(): JsonResponse
    {
        $data = $this->service->getManyByPagination(10);

        return $this->respondOk($data);
    }

    public function show(int $id): JsonResponse
    {
        $folder = $this->service->getById($id);
        if (! isset($folder)) {
            return $this->respondNotFound();
        }

        return $this->respondOk($folder);
    }

    public function store(StoreExamFolderRequest $request): JsonResponse
    {
        $id = $this->service->create($request->all(), $request->user()->id);

        return $this->respondCreated($id);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        if ($this->service->getById($id) === null) {
            return $this->respondNotFound();
        }

        $this->service->update($id, $request->all());

        return $this->respondNoContent();
    }

    public function destroy(int $id): JsonResponse
    {
        $this->service->deleteById($id);

        return $this->respondNoContent();
    }
}
