<?php

namespace App\Services\TrainingProgram;

use App\Models\TrainingProgram\TrainingProgramAttachment;
use Illuminate\Support\Collection;

class TrainingProgramAttachmentService
{
    public function getById(int $id): TrainingProgramAttachment
    {
        return TrainingProgramAttachment::findOrFail($id);
    }

    public function deleteById(int $id): bool
    {
        return TrainingProgramAttachment::findOrFail($id)->delete() === true;
    }

    public function create(array $data): int
    {
        return TrainingProgramAttachment::create($data)->id;
    }

    public function update(int $id, array $data): bool
    {
        return TrainingProgramAttachment::findOrFail($id)->update($data);
    }

    public function getByTrainingProgramId(int $id): Collection
    {
        return TrainingProgramAttachment::where('training_program_id', $id)->get();
    }
}
