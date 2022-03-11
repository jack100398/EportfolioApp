<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateFileRequest;
use App\Models\File;
use App\Services\File\FileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileController extends BaseApiController
{
    private FileService $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    public function show(int $fileId): StreamedResponse | JsonResponse
    {
        $file = File::find($fileId);
        if ($file === null) {
            return $this->respondNotFound();
        }

        return Storage::download($file->directory.'/'.$file->id, $file->name);
    }

    public function store(CreateFileRequest $request): JsonResponse
    {
        $id = $this->fileService->save($request->directory, $request->file, $request->user()->id);

        return $this->respondCreated($id);
    }
}
