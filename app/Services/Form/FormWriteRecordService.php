<?php

namespace App\Services\Form;

use App\Models\Form\FormWriteRecord;
use App\Services\Form\Enum\FormWriteRecordFlagEnum;
use App\Services\Form\Interfaces\IFormWriteRecordService;
use Illuminate\Support\Collection;

class FormWriteRecordService implements IFormWriteRecordService
{
    public function getById(int $id): FormWriteRecord
    {
        return FormWriteRecord::findOrFail($id);
    }

    public function create(array $data): FormWriteRecord
    {
        $data['result'] = json_encode($data['result']);

        return FormWriteRecord::create($data);
    }

    public function getResultWriteRecord(int $workflowId): ?FormWriteRecord
    {
        return FormWriteRecord::where([
            ['flag', FormWriteRecordFlagEnum::RESULT],
            ['workflow_id', $workflowId],
        ])->first();
    }

    public function getTempWriteRecord(int $workflowId): ?FormWriteRecord
    {
        return FormWriteRecord::where([
            ['workflow_id', $workflowId],
            ['flag', FormWriteRecordFlagEnum::TEMP],
        ])->first();
    }

    public function deleteById(int $id): bool
    {
        return FormWriteRecord::findOrFail($id)->delete() === true;
    }

    public function batchDeleteByWorkflowId(int $workflowId): void
    {
        FormWriteRecord::where([
            ['workflow_id', $workflowId],
            ['flag', FormWriteRecordFlagEnum::TEMP],
        ])->delete();
    }

    public function getRequiredWriteQuestionType(?int $role, array $questions, ?array $previousResults, array $results): Collection
    {
        return collect($questions)->map(function ($questionGroup, $key) use ($role, $previousResults, $results) {
            if (isset($questionGroup->attributes->questions)) {
                return is_null($previousResults) ?
                $this->transferQuestionType($role, $questionGroup->attributes->questions, null, $results[$key]) :
                $this->transferQuestionType($role, $questionGroup->attributes->questions, $previousResults[$key], $results[$key]);
            }
        })->filter(function ($result) {
            return ! is_null($result) && $result->count() > 0;
        });
    }

    /**
     * targets 有0為不限身分.
     */
    private function transferQuestionType(?int $role, array $questionTypes, ?array $previousResult, array $result): Collection
    {
        return collect($questionTypes)->map(function ($questionType, $questionTypeKey) use ($role, $previousResult, $result) {
            if (isset($questionType->attributes->targets) &&
            ((! in_array($role, $questionType->attributes->targets) && ! in_array(0, $questionType->attributes->targets)))) {
                if (isset($previousResult[$questionTypeKey]) && ($result[$questionTypeKey] !== $previousResult[$questionTypeKey]) || (! isset($previousResult[$questionTypeKey]) && ! empty($result[$questionTypeKey]))) {
                    return $questionTypeKey;
                }
            }

            if (isset($questionType->attributes->require) && $questionType->attributes->require === true && empty($result[$questionTypeKey])) {
                return $questionTypeKey;
            }
        })->filter(function ($result) {
            return ! is_null($result);
        });
    }
}
