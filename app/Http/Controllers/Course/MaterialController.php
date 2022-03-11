<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\Course\AuthMaterialRequest;
use App\Http\Requests\Course\CreateMaterialRequest;
use App\Http\Requests\Course\UpdateMaterialRequest;
use App\Models\Auth\User;
use App\Models\File;
use App\Models\Material\Material;
use App\Models\Material\MaterialAuthorize;
use App\Services\Course\MaterialService;
use App\Services\File\FileService;
use Hamcrest\Type\IsInteger;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use function PHPUnit\Framework\isNull;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MaterialController extends BaseApiController
{
    private const FILE = 0;

    private const URL = 1;

    private const FOLDER = 2;

    private FileService $fileService;

    private MaterialService $service;

    public function __construct(MaterialService $service, FileService $fileService)
    {
        $this->fileService = $fileService;
        $this->service = $service;
    }

    public function index(Request $request): JsonResponse
    {
        $materials = Material::with('belong')
            ->leftJoin('files', 'files.id', 'materials.source')
            ->orderBy('type', 'DESC');

        if ((! is_string($request->query('parentId')) && (int) $request->query('ownMaterial') === 2)) {
            $materials = $this->service->getAuthorizedMaterials($request->user()->id, $materials);
        } else {
            if ((int) $request->query('ownMaterial') === 1) {
                $materials->where('owner', $request->user()->id);
            }
            $materials = $this->service->getOwnMaterials(
                is_string($request->query('parentId')),
                (int) $request->query('parentId'),
                $materials
            );
        }

        return $this->respondOk($materials->get(['*', 'materials.id as materialId']));
    }

    public function getAuthorizedMaterials(Request $request): JsonResponse
    {
        $authorizedMaterials = MaterialAuthorize::where('authorize_type', User::class)->where('authorize_id', $request->user()->id)->pluck('material_id');

        $materials = Material::whereIn('id', $authorizedMaterials);

        $materials->with('belong')
        ->leftJoin('files', 'files.id', 'materials.source')
        ->orderBy('type', 'DESC');

        return $this->respondOk($materials->get(['*', 'materials.id as materialId']));
    }

    public function store(CreateMaterialRequest $request): JsonResponse
    {
        $data = $request->collect();
        $data->put('owner', $request->user()->id);

        if ($request->type === self::FILE || $request->type === strval(self::FILE)) {
            $data->put('source', $this->fileService->save(
                $request->directory,
                $request->source,
                $request->user()->id
            ));
        }

        return $this->respondCreated($this->service->create($data->all()));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteMaterialAndChildMaterials($id);

        return $this->respondNoContent();
    }

    private function deleteMaterialAndChildMaterials(int $id): void
    {
        $material = $this->service->getMaterialById($id);
        $material->children->each(function ($child) {
            $this->deleteMaterialAndChildMaterials($child->id);
        });
        $this->service->deleteMaterialById($id);
    }

    public function update(int $id, UpdateMaterialRequest $request): JsonResponse
    {
        $material = $this->service->getMaterialById($id);

        if ($material->type === 0) {
            $file = $this->fileService->getFileInfo((int) $material->source);
            $file->name = $request->source;
            $file->save();
            $this->service->update($id, $request->only('folder_id'));
        } else {
            $this->service->update($id, $request->all());
        }

        return $this->respondNoContent();
    }

    public function show(int $id): JsonResponse
    {
        return $this->respondOk($this->service->getMaterialById($id));
    }

    public function authUser(AuthMaterialRequest $request): JsonResponse
    {
        $this->service->authUser($request->id, $request->targetId);

        return $this->respondNoContent();
    }

    public function authUnit(AuthMaterialRequest $request): JsonResponse
    {
        $this->service->authUnit($request->id, $request->targetId);

        return $this->respondNoContent();
    }

    public function deAuthUser(AuthMaterialRequest $request): JsonResponse
    {
        $this->service->deAuthUser($request->id, $request->targetId);

        return $this->respondNoContent();
    }

    public function deAuthUnit(AuthMaterialRequest $request): JsonResponse
    {
        $this->service->deAuthUnit($request->id, $request->targetId);

        return $this->respondNoContent();
    }

    public function getAuthUnits(int $id): JsonResponse
    {
        $authUnits = $this->service->getMaterialById($id)->authUnit()->get();

        return $this->respondOk($authUnits);
    }

    public function getAuthUsers(int $id): JsonResponse
    {
        $authUnits = $this->service->getMaterialById($id)->authUser()->get();

        return $this->respondOk($authUnits);
    }

    public function downloadMaterial(int $id): StreamedResponse
    {
        $material = Material::findOrFail($id);

        $file = File::findOrFail($material->source);

        return Storage::download($file->directory.'/'.$file->id, $file->name);
    }

    public function downloadMaterialFolder(int $folderId): JsonResponse|BinaryFileResponse
    {
        $folder = Material::findOrFail($folderId);

        if ($folder->children()->count() === 0) {
            return $this->respondForbidden('No Any File In This Folder');
        }

        $zip = $this->fileService->createZipStream($folder->source);

        $zip = $this->createZipFolder($folder, '/', $zip);

        $this->fileService->endZipStream($zip);

        return response()->download($folder->source);
    }

    private function createZipFolder(Material $folder, string $currentPath, \ZipArchive $zip): \ZipArchive
    {
        $folder->children->each(function ($child) use (&$zip, $currentPath) {
            if ($child->type === self::FOLDER) {
                $zip = $this->createChildFolder($child, $currentPath, $zip);
            } else {
                $zip = $this->fileService->pushFileIntoFolder('material', (int) $child->source, $currentPath, $zip);
            }
        });

        return $zip;
    }

    private function createChildFolder(Material $folder, string $currentPath, \ZipArchive $zip): \ZipArchive
    {
        $nextPath = $currentPath.$folder->source.'/';
        $zip->addEmptyDir($nextPath);

        return $this->createZipFolder($folder, $nextPath, $zip);
    }
}
