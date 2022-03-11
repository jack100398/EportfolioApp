<?php

namespace App\Http\Controllers\Exam;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\Exam\StoreExamQuestionRequest;
use App\Models\Exam\QuestionMetadata\OptionFactory;
use App\Services\Exam\ExamQuestionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ExamQuestionController extends BaseApiController
{
    private ExamQuestionService $service;

    public function __construct(ExamQuestionService $service)
    {
        $this->service = $service;
    }

    public function show(int $id): JsonResponse
    {
        $question = $this->service->getById($id);
        if (! isset($question)) {
            return $this->respondNotFound();
        }

        return $this->respondOk($question);
    }

    public function store(StoreExamQuestionRequest $request): JsonResponse
    {
        $isMetadataValid = $this->verifyQuestionMetadata($request->metadata, $request->type);

        if (! $isMetadataValid) {
            return response()->json([
                'error' => 'Metadata format incorrect',
            ], Response::HTTP_BAD_REQUEST);
        }

        $id = $this->service->create($request->all(), $request->user()->id);

        return $this->respondCreated($id);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $question = $this->service->getById($id);

        if ($question === null) {
            return $this->respondNotFound();
        }

        $metadata = $request->metadata ?? $question->metadata;
        $type = $request->type ?? $question->type;
        $isMetadataValid = $this->verifyQuestionMetadata($metadata, $type);

        if (! $isMetadataValid) {
            return response()->json(
                ['error' => 'Metadata format incorrect'],
                Response::HTTP_BAD_REQUEST
            );
        }
        $this->service->update($id, $request->all());

        return $this->respondNoContent();
    }

    public function destroy(int $id): JsonResponse
    {
        $this->service->deleteById($id);

        return $this->respondNoContent();
    }

    public function verifyQuestionMetadata(array $metadata, int $type): bool
    {
        $validator = OptionFactory::make($type, $metadata);

        return $validator->verify();
    }
}
