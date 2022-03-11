<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\Course\CreateCourseMaterialRequest;
use App\Http\Requests\Course\UpdateCourseMaterialRequest;
use App\Models\File;
use App\Models\Material\CourseMaterial;
use App\Models\Material\Material;
use App\Services\Course\CourseMaterialService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CourseMaterialController extends BaseApiController
{
    private CourseMaterialService $service;

    public function __construct(CourseMaterialService $service)
    {
        $this->service = $service;
    }

    public function index(): JsonResponse
    {
        //TODO:MayBe CourseId or DoNothing
        $materials = CourseMaterial::where('course_id', 1)->get();

        return $this->respondOk($materials);
    }

    public function store(CreateCourseMaterialRequest $request): JsonResponse
    {
        $data = collect($request->all());

        $data->put('updated_by', $request->user()->id);
        $data->put('created_by', $request->user()->id);

        $id = $this->service->create($data->all());

        return $this->respondCreated($id);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->service->deleteById($id);

        return $this->respondNoContent();
    }

    public function update(int $id, UpdateCourseMaterialRequest $request): JsonResponse
    {
        $data = $request->all();
        $data['required_time'] = gmdate('H:i:s', $data['required_time']);

        $this->service->update($id, $data);

        return $this->respondNoContent();
    }

    public function show(int $id): JsonResponse
    {
        return $this->respondOk($this->service->getById($id));
    }

    public function downloadCourseMaterial(int $id): StreamedResponse | JsonResponse
    {
        $materialId = CourseMaterial::findOrFail($id)->material_id;

        if (! isset(Material::findOrFail($materialId)->source) || ! is_numeric(Material::findOrFail($materialId)->source)) {
            return $this->respondNotFound();
        }

        $fileId = Material::findOrFail($materialId)->source;

        $file = File::findOrFail($fileId);

        return Storage::download($file->directory.'/'.$file->id, $file->name);
    }
}
