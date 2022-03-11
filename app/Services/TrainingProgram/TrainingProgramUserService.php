<?php

namespace App\Services\TrainingProgram;

use App\Models\TrainingProgram\ModifiedRecord\TrainingProgramUserModifiedRecord;
use App\Models\TrainingProgram\TrainingProgram;
use App\Models\TrainingProgram\TrainingProgramUser;

class TrainingProgramUserService
{
    public function getById(int $id): TrainingProgramUser
    {
        return TrainingProgramUser::findOrFail($id);
    }

    public function deleteById(int $id): bool
    {
        $programUser = TrainingProgramUser::findOrFail($id);
        $result = $programUser->delete() === true;

        $this->createModifiedRecord($programUser, TrainingProgramUserModifiedRecord::DELETED);

        return $result;
    }

    public function create(array $data): int
    {
        $programUser = TrainingProgramUser::create($data);

        $this->syncProgramUsers($programUser);
        $this->createModifiedRecord($programUser, TrainingProgramUserModifiedRecord::CREATED);

        return $programUser->id;
    }

    public function update(int $id, array $data): bool
    {
        $programUser = TrainingProgramUser::findOrFail($id);
        $result = $programUser->update($data);
        $this->createModifiedRecord($programUser, TrainingProgramUserModifiedRecord::UPDATED);

        return $result;
    }

    /**
     * 留下修改紀錄.
     *
     * @param TrainingProgramUser $user
     * @param int $action
     *
     * @return void
     */
    private function createModifiedRecord(TrainingProgramUser $user, int $action): void
    {
        TrainingProgramUserModifiedRecord::create([
            'action' => $action,
            'training_program_id' => $user->training_program_id,
            'user_id' => $user->user_id,
            'phone_number' => $user->phone_number,
            'group_name' => $user->group_name,
            'created_by' => auth()->user()?->id,
        ]);
    }

    /**
     * 同步計畫學生
     *
     * @param TrainingProgramUser $trainingProgramUser
     *
     * @return void
     */
    private function syncProgramUsers(TrainingProgramUser $trainingProgramUser): void
    {
        // 取得子計畫
        $trainingPrograms = $trainingProgramUser->trainingProgram?->syncedToPrograms;
        if ($trainingPrograms === null) {
            return;
        }
        // 建立子計畫的學生
        $trainingPrograms->each(function ($program) use ($trainingProgramUser) {
            $user = $this->getProgramUser($program, $trainingProgramUser->user_id);
            if ($user !== null) {
                return true;
            }
            TrainingProgramUser::create([
                'training_program_id' => $program->id,
                'user_id' => $trainingProgramUser->user_id,
                'phone_number' => $trainingProgramUser->phone_number,
                'group_name' => $trainingProgramUser->group_name,
            ]);
        });
    }

    private function getProgramUser(TrainingProgram $program, int $userId): ?TrainingProgramUser
    {
        return TrainingProgramUser::where([
            'training_program_id' => $program->id,
            'user_id' => $userId,
        ])->first();
    }
}
