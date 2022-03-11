<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\Course\CreateFeedBackRequest;
use App\Http\Requests\Course\UpdateFeedBackRequest;
use App\Services\Course\FeedbackService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FeedbackController extends BaseApiController
{
    private FeedbackService $service;

    public function __construct(FeedbackService $service)
    {
        $this->service = $service;
    }

    public function store(CreateFeedBackRequest $request): JsonResponse
    {
        return $this->respondCreated($this->service->create($request->all(), $request->user()->id));
    }

    public function show(int $id): JsonResponse
    {
        return $this->respondOk($this->service->getById($id));
    }

    public function update(int $id, UpdateFeedBackRequest $request): JsonResponse
    {
        $this->service->update($id, $request->all());

        return $this->respondNoContent();
    }

    public function destroy(int $id): JsonResponse
    {
        $this->service->delete($id);

        return $this->respondNoContent();
    }
}
