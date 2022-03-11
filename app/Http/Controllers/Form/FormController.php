<?php

namespace App\Http\Controllers\Form;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\Form\CreateFormRequest;
use App\Http\Requests\Form\FormCopyRequest;
use App\Http\Requests\Form\FormEnabledRequest;
use App\Http\Requests\Form\FormListRequest;
use App\Http\Requests\Form\FormSendListRequest;
use App\Http\Requests\Form\ReviewedListRequest;
use App\Http\Requests\Form\ReviewedUpdateFormRequest;
use App\Http\Requests\Form\UpdateFormBaseSettingRequest;
use App\Http\Requests\Form\UpdateFormRequest;
use App\Services\Form\Enum\ReviewedEnum;
use App\Services\Form\Interfaces\IFormService;
use App\Services\Form\Interfaces\IFormUnitService;
use App\Services\Form\QuestionTypeFactory;
use App\Services\Interfaces\IDefaultWorkflowService;
use Illuminate\Http\JsonResponse;

class FormController extends BaseApiController
{
    private IFormService $service;

    private IFormUnitService $formUnitService;

    private IDefaultWorkflowService $defaultWorkflowService;

    private QuestionTypeFactory $questionTypeFactory;

    public function __construct(
        IFormService $service,
        IFormUnitService $formUnitService,
        IDefaultWorkflowService $defaultWorkflowService,
        QuestionTypeFactory $questionTypeFactory
    ) {
        $this->service = $service;
        $this->formUnitService = $formUnitService;
        $this->defaultWorkflowService = $defaultWorkflowService;
        $this->questionTypeFactory = $questionTypeFactory;
    }

    public function index(FormListRequest $request): JsonResponse
    {
        return $this->respondOk($this->service->getManyByPagination($request->all()));
    }

    public function store(CreateFormRequest $request): JsonResponse
    {
        $formRequest = $request->except(['unit_ids']);
        $transferQuestionType = $this->transferQuestionGroup($request->questions);

        if (! $this->checkFormStoreCondition(
            $transferQuestionType,
            array_unique(
                array_merge($request->form_default_workflow, [$request->course_form_default_assessment])
            )
        )) {
            return $this->respondNotFound();
        }

        $questionTypeJson = json_encode($transferQuestionType);
        $formRequest['questions'] = $questionTypeJson === false ? json_encode([]) : $questionTypeJson;
        $form = $this->service->create($this->transferFormRequest($formRequest));

        $this->formUnitService->storeFormUnit($form->id, $request->unit_ids);

        return $this->respondCreated($form->id);
        //TODO:send mail
    }

    public function update(UpdateFormRequest $request, int $id): JsonResponse
    {
        $form = $this->service->getById($id);
        $transferQuestionType = $this->transferQuestionGroup($request->questions);

        if (! $this->checkFormStoreCondition(
            $transferQuestionType,
            array_unique(
                array_merge($request->form_default_workflow, [$request->course_form_default_assessment])
            )
        )) {
            return $this->respondNotFound();
        }

        $newForm = $this->service->updateForm(
            $this->transferFormRequest($request->except(['unit_ids'])),
            $form,
            $transferQuestionType
        );

        $this->formUnitService->storeFormUnit($newForm->id, $request->unit_ids);
        //TODO:send mail and update course and threshold form

        return $this->respondOk($newForm);
    }

    public function show(int $id): JsonResponse
    {
        return $this->respondOk($this->service->getById($id));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->service->getById($id)->delete();

        return $this->respondNoContent();
    }

    public function showReviewedFormList(ReviewedListRequest $request): JsonResponse
    {
        return $this->respondOk($this->service->getReviewedByPagination($request->all()));
    }

    /**
     * 批次更新需要審查的表單.
     */
    public function updateReviewedForm(ReviewedUpdateFormRequest $request): JsonResponse
    {
        return $this->respondOk($this->service->batchUpdateFormReviewed(
            $request->form_ids,
            $request->reviewed
        ));
    }

    /**
     * 表單統計範圍配置 (顯示表單列表與有發送的數量).
     */
    public function getFormSendList(FormSendListRequest $request): JsonResponse
    {
        return $this->respondOk($this->service->getWorkflowFormByPagination($request->all()));
    }

    public function updateFormBaseSetting(UpdateFormBaseSettingRequest $request, int $id): JsonResponse
    {
        $form = $this->service->updateFormBaseSetting($id, $request->all());
        $this->formUnitService->storeFormUnit($id, $request->unit_ids);

        return $this->respondOk($form);
    }

    public function formEnabled(FormEnabledRequest $request, int $id): JsonResponse
    {
        $this->service->updateFormEnable($id, $request->is_enabled);

        return $this->respondNoContent();
    }

    public function formCopy(FormCopyRequest $request): JsonResponse
    {
        $form = $this->service->getById($request->form_id);
        $form->reviewed = $this->getReviewed($form->reviewed);
        $newForm = $this->service->storeFormCopy($form, $request->except('unit_id'));
        $this->formUnitService->storeFormUnit($newForm->id, $request->unit_ids);

        return $this->respondCreated($newForm->id);
    }

    private function checkFormStoreCondition(array $transferQuestionType, array $defaultWorkflows): bool
    {
        return count($transferQuestionType) === 0
            || ! $this->defaultWorkflowService->checkDefaultWorkflow($defaultWorkflows) ? false : true;
    }

    /**
     * 轉換Question type return array
     * 表單第一層會有題組、說明.
     */
    private function transferQuestionGroup(array $questionGroups): array
    {
        return collect($questionGroups)->map(function ($questionGroup) {
            if (isset($questionGroup['attributes']['questions'])) {
                $questionGroup['attributes']['questions'] =
                $this->transferQuestionType($questionGroup['attributes']['questions']);
            }

            return $this->questionTypeFactory->getQuestionType($questionGroup);
        })->reject(function ($transferQuestionType) {
            return count($transferQuestionType) === 0;
        })->toArray();
    }

    /**
     * 轉換Question type return array
     * 第二層會有題目 (第三層可能會有題目選項(像是複選題) 寫在各自的題目工廠裡面).
     */
    private function transferQuestionType(array $questionTypes): array
    {
        return collect($questionTypes)->map(function ($questionType) {
            return $this->questionTypeFactory->getQuestionType($questionType);
        })->reject(function ($transferQuestionType) {
            return count($transferQuestionType) === 0;
        })->toArray();
    }

    private function transferFormRequest(array $request): array
    {
        $request['name'] = ! isset($request['name']) ? '自訂表單'.date('Y-m-d H:i') : $request['name'];
        //TODO:check tmu
        $request['reviewed'] = $this->getReviewed($request['reviewed']);
        if (isset($request['is_writable'])) {
            $isWritableJson = json_encode($request['is_writable']);
            $request['is_writable'] = $isWritableJson === false ? json_encode([]) : $isWritableJson;
        }
        $formDefaultWorkflowJson = json_encode($request['form_default_workflow']);
        $request['form_default_workflow'] = $formDefaultWorkflowJson ?
        $formDefaultWorkflowJson : '[]';

        return $request;
    }

    private function getReviewed(int $reviewed): int
    {
        return $reviewed === ReviewedEnum::UNAPPROVED || $reviewed === ReviewedEnum::EDIT ?
            $reviewed : ReviewedEnum::PASS;
    }
}
