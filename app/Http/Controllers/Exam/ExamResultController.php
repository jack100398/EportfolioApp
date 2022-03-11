<?php

namespace App\Http\Controllers\Exam;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\Exam\StoreExamResultRequest;
use App\Services\Exam\ExamResultService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExamResultController extends BaseApiController
{
    private ExamResultService $service;

    public function __construct(ExamResultService $service)
    {
        $this->service = $service;
    }

    public function show(int $id): JsonResponse
    {
        $result = $this->service->getById($id);
        if (! isset($result)) {
            return $this->respondNotFound();
        }

        return $this->respondOk($result);
    }

    public function store(StoreExamResultRequest $request): JsonResponse
    {
        $data = $request->except([
            'metadata',
            'score',
            'is_marked',
            'is_finished',
            'source_ip',
        ]);
        $data['source_ip'] = $request->ip();

        $id = $this->service->create($data);

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

    public function autoMarkScore(int $id): JsonResponse
    {
        $result = $this->service->autoMarkingResult($id);

        if (! $result) {
            return $this->respondNotFound();
        }

        return $this->respondNoContent();
    }

    public function manualMarkScore(Request $request, int $id): JsonResponse
    {
        $scores = $request->all();
        $result = $this->service->manualMarkingResult($id, $scores);

        if (! $result) {
            return $this->respondNotFound();
        }

        return $this->respondNoContent();
    }
}
