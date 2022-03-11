<?php

namespace App\Http\Controllers\Exam;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\Exam\StoreExamRequest;
use App\Services\Exam\ExamService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExamController extends BaseApiController
{
    private ExamService $service;

    public function __construct(ExamService $service)
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
        $exam = $this->service->getById($id);
        if (! isset($exam)) {
            return $this->respondNotFound();
        }

        return $this->respondOk($exam);
    }

    public function store(StoreExamRequest $request): JsonResponse
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

    public function showWithoutAnswer(int $id): JsonResponse
    {
        $data = $this->service->getQuestionsWithoutAnswerById($id);

        return $this->respondOk($data);
    }

    public function showTemplates(): JsonResponse
    {
        $templates = $this->service->getTemplateExams();

        return $this->respondOk($templates);
    }
}
