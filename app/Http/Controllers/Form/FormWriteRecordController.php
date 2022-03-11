<?php

namespace App\Http\Controllers\Form;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\Form\CreateWriteRecordRequest;
use App\Models\Workflow\Process;
use App\Services\Form\Enum\FormWriteRecordFlagEnum;
use App\Services\Form\Interfaces\IFormWriteRecordService;
use App\Services\Interfaces\IProcessService;
use App\Services\Interfaces\IWorkflowService;
use Illuminate\Http\JsonResponse;

class FormWriteRecordController extends BaseApiController
{
    private IWorkflowService $workflowService;

    private IProcessService $processService;

    private IFormWriteRecordService $formWriteRecordService;

    public function __construct(
        IWorkflowService $workflowService,
        IProcessService $processService,
        IFormWriteRecordService $formWriteRecordService
    ) {
        $this->workflowService = $workflowService;
        $this->processService = $processService;
        $this->formWriteRecordService = $formWriteRecordService;
    }

    /**
     * 判斷是否是提交或是暫存填寫資料,提交資料時將暫存資料全部刪除.
     */
    public function store(CreateWriteRecordRequest $request): JsonResponse
    {
        $process = $this->processService->getById($request->process_id);
        $workflow = $this->workflowService->getByIdWithForm($process->workflow_id);
        if (is_null($workflow) || is_null($workflow->form)
         || is_null(json_decode($workflow->form->questions)) ||
         $this->checkQuestionTypeNeedRequire(json_decode($workflow->form->questions), $process, $request)
         ) {
            return $this->respondNotFound();
        }

        $requestArray = $request->all();
        $requestArray['workflow_id'] = $workflow->id;
        $this->formWriteRecordService->batchDeleteByWorkflowId($workflow->id);

        return $this->respondCreated($this->formWriteRecordService->create($requestArray)->id);
    }

    public function show(int $id): JsonResponse
    {
        return $this->respondOk($this->formWriteRecordService->getById($id));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->formWriteRecordService->deleteById($id);

        return $this->respondNoContent();
    }

    /*
     * 獲得簽核結果.
     */
    public function getResultFormWriteRecord(int $workflowId): JsonResponse
    {
        $result = $this->workflowService->getById($workflowId);

        $result = $this->formWriteRecordService->getResultWriteRecord($workflowId);

        return is_null($result) ? $this->respondNotFound() : $this->respondOk($result);
    }

    private function getPreviousResultRecord(int $workflowId): ?array
    {
        $previousResult = $this->formWriteRecordService->getResultWriteRecord($workflowId);

        return is_null($previousResult) || json_decode($previousResult->result) === false ? null : json_decode($previousResult->result);
    }

    private function checkQuestionTypeNeedRequire(array $questions, Process $process, CreateWriteRecordRequest $request): bool
    {
        if (is_null($process->role)) {
            return true;
        }
        $jsonPreviousResult = $this->getPreviousResultRecord($process->workflow_id);

        $checkRequired = $this->formWriteRecordService->getRequiredWriteQuestionType($process->role, $questions, $jsonPreviousResult, $request->result);

        return $request->flag === FormWriteRecordFlagEnum::RESULT && $checkRequired->count() > 0;
    }
}
