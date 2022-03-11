<?php

namespace App\Services\Workflow;

use App\Models\Workflow\ScheduleSendWorkflowForm;
use App\Services\Interfaces\IScheduleSendWorkflowFormService;
use Illuminate\Support\Collection;

class ScheduleSendWorkflowFormService implements IScheduleSendWorkflowFormService
{
    public function getMany(int $limit): Collection
    {
        return ScheduleSendWorkflowForm::orderBy('id')->take($limit)->get();
    }

    public function getByOne(): ?ScheduleSendWorkflowForm
    {
        return ScheduleSendWorkflowForm::orderByDesc('id')->first();
    }

    public function getSentForm(int $max): Collection
    {
        return ScheduleSendWorkflowForm::where('start_at', date('Y-m-d'))->take($max)->get();
    }

    public function getQueueForm(int $key_id, int $studentId): ?ScheduleSendWorkflowForm
    {
        return ScheduleSendWorkflowForm::where([['key_id', $key_id], ['student_id', $studentId]])->first();
    }

    public function deleteById(int $id): bool
    {
        return ScheduleSendWorkflowForm::findOrFail($id)->delete() === true;
    }
}
