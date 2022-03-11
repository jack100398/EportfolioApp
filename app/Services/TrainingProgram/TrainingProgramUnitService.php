<?php

namespace App\Services\TrainingProgram;

use App\Models\TrainingProgram\TrainingProgramUnit;
use Illuminate\Support\Collection;

class TrainingProgramUnitService
{
    public function getById(int $id): TrainingProgramUnit
    {
        return TrainingProgramUnit::findOrFail($id);
    }

    public function deleteById(int $id): bool
    {
        return TrainingProgramUnit::findOrFail($id)->delete() === true;
    }

    public function create(array $data): int
    {
        return TrainingProgramUnit::create($data)->id;
    }

    public function cloneProgramUnits(Collection $programUnits, int $trainingProgramId): array
    {
        $map = [];
        $programUnits->each(
            function (TrainingProgramUnit $programUnit) use ($trainingProgramId, &$map) {
                $newProgramUnit = $programUnit->replicate();
                $newProgramUnit->training_program_id = $trainingProgramId;
                $newProgramUnit->save();

                $map[$programUnit->id] = $newProgramUnit->id;
            }
        );

        return $map;
    }
}
