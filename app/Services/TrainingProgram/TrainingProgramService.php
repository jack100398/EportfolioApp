<?php

namespace App\Services\TrainingProgram;

use App\Models\TrainingProgram\ModifiedRecord\TrainingProgramUserModifiedRecord;
use App\Models\TrainingProgram\TrainingProgram;
use App\Models\TrainingProgram\TrainingProgramSync;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class TrainingProgramService
{
    public function getManyByPagination(int $perPage): LengthAwarePaginator
    {
        return TrainingProgram::with('attachments')->paginate($perPage);
    }

    public function getById(int $id): TrainingProgram
    {
        return TrainingProgram::with(
            'users',
            'units',
            'programUsers.steps',
            'programUnits',
            'programCategories',
            'attachments',
        )->findOrFail($id);
    }

    /**
     * 取得複製計畫需要的所有資料.
     *
     * @param int $id
     *
     * @return TrainingProgram
     */
    public function getCopyDataById(int $id): TrainingProgram
    {
        return TrainingProgram::with(
            'programUnits.nominalRoleUsers',
            'programCategories.courses',
            'nominalRoleUsers'
        )->findOrFail($id);
    }

    public function cloneTrainingProgram(
        TrainingProgram $program,
        int $year,
        string $name,
        Carbon $startDate,
        Carbon $endDate
    ): TrainingProgram {
        $newProgram = $program->replicate();
        $newProgram->year = $year;
        $newProgram->name = $name;
        $newProgram->start_date = $startDate;
        $newProgram->end_date = $endDate;
        $newProgram->save();

        return $newProgram;
    }

    public function deleteById(int $id): bool
    {
        return TrainingProgram::findOrFail($id)->delete() === true;
    }

    public function create(array $data): int
    {
        return TrainingProgram::create($data)->id;
    }

    public function update(int $id, array $data): bool
    {
        return TrainingProgram::findOrFail($id)->update($data);
    }

    public function getAuthUnit(int $id): Collection
    {
        return TrainingProgram::findOrFail($id)->authUnits;
    }

    public function createAuthUnit(int $id, array $unitIds): bool
    {
        TrainingProgram::findOrFail($id)->authUnits()->attach($unitIds);

        return true;
    }

    public function deleteAuthUnit(int $id, int $unitId): bool
    {
        return TrainingProgram::findOrFail($id)->authUnits()->detach($unitId) > 0;
    }

    public function getSyncedProgram(int $id): array
    {
        $data = [
            'from_programs' => [],
            'to_programs' => [],
        ];

        TrainingProgramSync::with('fromTrainingProgram', 'toTrainingProgram')
            ->where('from_training_program_id', $id)
            ->orWhere('to_training_program_id', $id)
            ->get()
            ->each(function ($program) use ($id, &$data) {
                if ($program->to_training_program_id === $id) {
                    $data['from_programs'][] = $program->fromTrainingProgram;
                } else {
                    $data['to_programs'][] = $program->toTrainingProgram;
                }
            });

        return $data;
    }

    public function syncProgram(int $fromProgramId, int $toProgramId): bool
    {
        TrainingProgramSync::create([
            'from_training_program_id' => $fromProgramId,
            'to_training_program_id' => $toProgramId,
        ]);

        return true;
    }

    public function unSyncProgram(int $fromProgramId, int $toProgramId): bool
    {
        return TrainingProgramSync::where([
            'from_training_program_id' => $fromProgramId,
            'to_training_program_id' => $toProgramId,
        ])->delete() > 0;
    }

    public function getUserRecord(int $id): Collection
    {
        return TrainingProgramUserModifiedRecord::where('training_program_id', $id)
            ->orderBy('created_at')
            ->get();
    }
}
