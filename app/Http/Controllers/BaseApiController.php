<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class BaseApiController extends Controller
{
    protected function respondOk(object | array $data): JsonResponse
    {
        return $this->respond($data, Response::HTTP_OK);
    }

    protected function respondCreated(int $id): JsonResponse
    {
        return $this->respond(['id' => $id], Response::HTTP_CREATED);
    }

    protected function respondNoContent(): JsonResponse
    {
        return $this->respond(null, Response::HTTP_NO_CONTENT);
    }

    protected function respondUnauthorized(string $message = 'Unauthorized'): JsonResponse
    {
        return $this->respondError($message, Response::HTTP_UNAUTHORIZED);
    }

    protected function respondForbidden(string $message = 'Forbidden'): JsonResponse
    {
        return $this->respondError($message, Response::HTTP_FORBIDDEN);
    }

    protected function respondNotFound(string $message = 'Not Found'): JsonResponse
    {
        return $this->respondError($message, Response::HTTP_NOT_FOUND);
    }

    protected function respondInternalError(string $message = 'Internal Error'): JsonResponse
    {
        return $this->respondError($message, Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Return generic json respond with the given data.
     */
    private function respond(mixed $data, int $statusCode): JsonResponse
    {
        return response()->json($data, $statusCode);
    }

    private function respondError(string $message, int $statusCode): JsonResponse
    {
        return $this->respond([
            'errors' => [
                // 'code' => $statusCode,
                'message' => $message,
            ],
        ], $statusCode);
    }
}
