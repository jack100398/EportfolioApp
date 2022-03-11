<?php

namespace App\Services\TrainingProgram;

use App\Models\TrainingProgram\ModifiedRecord\TrainingProgramStepModifiedRecord;
use App\Models\TrainingProgram\TrainingProgram;
use App\Models\TrainingProgram\TrainingProgramStep;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class TrainingProgramStepService
{
    public function getById(int $id): TrainingProgramStep
    {
        return TrainingProgramStep::with('programUnit', 'programUser')->findOrFail($id);
    }

    public function deleteById(int $id): bool
    {
        $programStep = TrainingProgramStep::findOrFail($id);
        $result = $programStep->delete() === true;

        $this->createModifiedRecord($programStep, TrainingProgramStepModifiedRecord::UPDATED);

        return $result;
    }

    public function create(array $data): int
    {
        $programStep = TrainingProgramStep::create($data);
        $this->syncProgramSteps($programStep);
        $this->createModifiedRecord($programStep, TrainingProgramStepModifiedRecord::CREATED);

        return $programStep->id;
    }

    public function update(int $id, array $data): bool
    {
        $programStep = TrainingProgramStep::findOrFail($id);
        $result = $programStep->update($data);

        $this->createModifiedRecord($programStep, TrainingProgramStepModifiedRecord::UPDATED);

        return $result;
    }

    public function getUserSteps(int $userId): Collection
    {
        return TrainingProgramStep::whereHas(
            'programUser',
            fn (Builder $query) => $query->where('user_id', $userId)
        )->get();
    }

    /**
     * 建立修改紀錄.
     *
     * @return void
     */
    private function createModifiedRecord(TrainingProgramStep $step, int $action): void
    {
        TrainingProgramStepModifiedRecord::create([
            'action' => $action,
            'program_user_id' => $step->program_user_id,
            'program_unit_id' => $step->program_unit_id,
            'name' => $step->name,
            'start_date' => $step->start_date,
            'end_date' => $step->end_date,
            'remarks' => $step->remarks,
            'created_by' => auth()->user()?->id,
        ]);
    }

    /**
     * 更新同步計畫.
     *
     * @param TrainingProgramStep $step
     *
     * @return void
     */
    private function syncProgramSteps(TrainingProgramStep $step): void
    {
        // 判斷是否為第一個站別
        if (
            $step->programUser === null ||
            $this->getProgramStepsCount($step->programUser->id) !== 1
        ) {
            return;
        }

        // 取得子計畫
        $trainingPrograms = $step->trainingProgram?->syncedToPrograms;
        if ($trainingPrograms === null) {
            return;
        }

        // 建立子計畫的學生站別
        $trainingPrograms->each(function ($program) use ($step) {
            if ($step->user === null) {
                return true;
            }

            $programUserId = $this->getProgramUserId($program, $step->user->id);
            // 還沒建立任何站別，才建立子計畫站別
            if ($programUserId === null || $this->getProgramStepsCount($programUserId) > 0) {
                return true;
            }

            $this->createStepsFromTemplate($program, $programUserId, $step->start_date);
        });
    }

    /**
     * 透過樣板建立訓練計畫.
     *
     * @param TrainingProgram $trainingProgram
     * @param int $programUserId
     * @param Carbon $startDate
     *
     * @return void
     */
    private function createStepsFromTemplate(
        TrainingProgram $trainingProgram,
        int $programUserId,
        Carbon $startDate
    ): void {
        $trainingProgram->stepsTemplate
            ->each(function ($stepTemplate) use ($programUserId, $startDate) {
                TrainingProgramStep::create([
                    'program_unit_id' => $stepTemplate->program_unit_id,
                    'program_user_id' => $programUserId,
                    'name' => '',
                    'start_date' => $startDate,
                    'end_date' => $startDate->clone()->addDays($stepTemplate->days),
                    'remarks' => '',
                ]);
                $startDate->addDays($stepTemplate->days + 1);
            });
    }

    private function getProgramUserId(TrainingProgram $trainingProgram, ?int $userId): ?int
    {
        if ($userId === null) {
            return null;
        }

        return $trainingProgram
            ->programUsers
            ->where('user_id', $userId)
            ->first()?->id;
    }

    private function getProgramStepsCount(int $programUserId): int
    {
        return TrainingProgramStep::where('program_user_id', $programUserId)->count();
    }
}
