<?php

namespace App\Services\TrainingProgram;

use App\Models\TrainingProgram\TrainingProgramStepTemplate;
use Illuminate\Support\Collection;

class TrainingProgramStepTemplateService
{
    public function getById(int $id): TrainingProgramStepTemplate
    {
        return TrainingProgramStepTemplate::findOrFail($id);
    }

    public function deleteById(int $id): bool
    {
        return TrainingProgramStepTemplate::findOrFail($id)->delete() === true;
    }

    public function create(array $data): int
    {
        return TrainingProgramStepTemplate::create($data)->id;
    }

    public function update(int $id, array $data): bool
    {
        return TrainingProgramStepTemplate::findOrFail($id)->update($data);
    }

    public function getByTrainingProgramId(int $id): Collection
    {
        return TrainingProgramStepTemplate::where('training_program_id', $id)->get();
    }
}
