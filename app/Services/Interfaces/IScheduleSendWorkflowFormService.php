<?php

namespace App\Services\Interfaces;

use App\Models\Workflow\ScheduleSendWorkflowForm;
use Illuminate\Support\Collection;

interface IScheduleSendWorkflowFormService
{
    public function getMany(int $limit): Collection;

    public function getByOne(): ?ScheduleSendWorkflowForm;

    public function getSentForm(int $max): Collection;

    public function getQueueForm(int $key_id, int $studentId): ?ScheduleSendWorkflowForm;

    public function deleteById(int $id): bool;
}
