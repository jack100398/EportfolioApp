<?php

namespace App\Http\Controllers\TrainingProgram;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\TrainingProgram\StoreProgramRequest;
use App\Services\Course\CourseService;
use App\Services\NominalRole\NominalRoleUserService;
use App\Services\TrainingProgram\TrainingProgramCategoryService;
use App\Services\TrainingProgram\TrainingProgramService;
use App\Services\TrainingProgram\TrainingProgramUnitService;
use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class TrainingProgramController extends BaseApiController
{
    private TrainingProgramService $service;

    private TrainingProgramCategoryService $categoryService;

    private TrainingProgramUnitService $programUnitService;

    private CourseService $courseService;

    private NominalRoleUserService $nominalRoleUserService;

    public function __construct(
        TrainingProgramService $service,
        TrainingProgramCategoryService $categoryService,
        CourseService $courseService,
        TrainingProgramUnitService $programUnitService,
        NominalRoleUserService $nominalRoleUserService
    ) {
        $this->service = $service;
        $this->categoryService = $categoryService;
        $this->courseService = $courseService;
        $this->programUnitService = $programUnitService;
        $this->nominalRoleUserService = $nominalRoleUserService;
    }

    public function index(): JsonResponse
    {
        $data = $this->service->getManyByPagination(10);

        return $this->respondOk($data);
    }

    public function show(int $id): JsonResponse
    {
        $exam = $this->service->getById($id);

        return $this->respondOk($exam);
    }

    public function store(StoreProgramRequest $request): JsonResponse
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

    public function showAuthUnit(int $id): JsonResponse
    {
        $result = $this->service->getAuthUnit($id);

        return $this->respondOk($result);
    }

    public function storeAuthUnit(Request $request, int $id): JsonResponse
    {
        $unitIds = $request->all();
        $this->service->createAuthUnit($id, $unitIds);

        return $this->respondNoContent();
    }

    public function destroyAuthUnit(int $id, int $unitId): JsonResponse
    {
        $result = $this->service->deleteAuthUnit($id, $unitId);
        if (! $result) {
            return $this->respondNotFound();
        }

        return $this->respondNoContent();
    }

    public function getSyncedProgram(int $id): JsonResponse
    {
        $result = $this->service->getSyncedProgram($id);

        return $this->respondOk($result);
    }

    public function sync(Request $request): JsonResponse
    {
        $this->service->syncProgram(
            $request->input('from_training_program_id'),
            $request->input('to_training_program_id'),
        );

        return $this->respondNoContent();
    }

    public function unSync(int $fromId, int $toId): JsonResponse
    {
        $result = $this->service->unSyncProgram($fromId, $toId);
        if (! $result) {
            return $this->respondNotFound();
        }

        return $this->respondNoContent();
    }

    public function userRecord(int $id): JsonResponse
    {
        $records = $this->service->getUserRecord($id);

        return $this->respondOk($records);
    }

    public function copy(Request $request): JsonResponse
    {
        $programId = $request->programId;
        $year = $request->year;
        $name = $request->name;
        $doCopyCourse = $request->doCopyCourse;

        try {
            $startDate = Carbon::createFromFormat('Y-m-d', $request->startDate);
            $endDate = Carbon::createFromFormat('Y-m-d', $request->endDate);
        } catch (InvalidFormatException $th) {
            $startDate = false;
            $endDate = false;
        }

        if (! $startDate instanceof Carbon || ! $endDate instanceof Carbon) {
            return $this->respondForbidden('????????????');
        }

        return DB::transaction(function () use ($programId, $year, $name, $startDate, $endDate, $doCopyCourse) {
            // ????????????
            $program = $this->service->getCopyDataById($programId);
            $newProgram = $this->service->cloneTrainingProgram($program, $year, $name, $startDate, $endDate);

            // ??????????????????
            $categories = $program->programCategories;
            $categoriesMap = $this->categoryService->cloneCategories($categories, $newProgram->id);

            // ??????????????????
            $programUnits = $program->programUnits;
            $programUnitsMap = $this->programUnitService->cloneProgramUnits($programUnits, $newProgram->id);

            // ?????????????????????????????????
            $this->nominalRoleUserService->cloneNominalRoleUsers($program->nominalRoleUsers, $newProgram->id);
            $programUnits->each(function ($programUnit) use ($programUnitsMap) {
                $roleUsers = $programUnit->nominalRoleUsers;
                $newProgramUnitId = $programUnitsMap[$programUnit->id];
                $this->nominalRoleUserService->cloneNominalRoleUsers($roleUsers, $newProgramUnitId);
            });

            // ???????????????????????????
            if ($doCopyCourse) {
                $courses = $categories->map(fn ($c) => $c->courses)->flatten();
                $this->courseService->cloneCourses($courses, $categoriesMap);
            }

            return $this->respondCreated($newProgram->id);
        });
    }
}
