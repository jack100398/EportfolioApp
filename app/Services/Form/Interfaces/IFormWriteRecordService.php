<?php

namespace App\Services\Form\Interfaces;

use App\Models\Form\FormWriteRecord;
use Illuminate\Support\Collection;

interface IFormWriteRecordService
{
    public function getById(int $id): FormWriteRecord;

    public function create(array $data): FormWriteRecord;

    public function getResultWriteRecord(int $workflowId): ?FormWriteRecord;

    public function getTempWriteRecord(int $workflowId): ?FormWriteRecord;

    public function deleteById(int $id): bool;

    public function batchDeleteByWorkflowId(int $workflowId): void;

    /**
     * 主要篩選 require , targets
     * 轉換Question type return array
     * 表單第一層會有題組、說明.
     */
    public function getRequiredWriteQuestionType(?int $role, array $questions, ?array $previousResults, array $results): Collection;
}
