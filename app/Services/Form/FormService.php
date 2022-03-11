<?php

namespace App\Services\Form;

use App\Models\Form\Form;
use App\Services\Form\Enum\ReviewedEnum;
use App\Services\Form\Interfaces\IFormService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Support\Collection;

class FormService implements IFormService
{
    public function create(array $data): Form
    {
        return Form::create($data);
    }

    public function getManyByPagination(array $condition): LengthAwarePaginator
    {
        $form = $this->checkFormUnitId($condition)->select(
            'name',
            'type',
            'version',
            'is_writable',
            'reviewed',
            'is_enabled',
            'is_sharable'
        );
        if (isset($condition['is_sharable'])) {
            $form->where('is_sharable', $condition['is_sharable']);
        }
        if (isset($condition['name'])) {
            $form->where('name', 'like', '%'.$condition['name'].'%');
        }

        return $form->orderByDesc('id')->paginate($condition['per_page']);
    }

    public function getById(int $id): Form
    {
        return Form::findOrFail($id);
    }

    public function getReviewedByPagination(array $condition): LengthAwarePaginator
    {
        $form = Form::where('reviewed', ReviewedEnum::UNAPPROVED)->with('formUnit');

        if (isset($condition['name'])) {
            $form->where('name', 'like', '%'.$condition['name'].'%');
        }

        return $form->orderByDesc('id')->paginate($condition['per_page']);
    }

    public function getReviewedForm(int $id): ?Form
    {
        return Form::where('reviewed', ReviewedEnum::UNAPPROVED)->find($id);
    }

    public function getWorkflowFormByPagination(array $conditions): LengthAwarePaginator
    {
        $form = Form::where('is_sharable', $conditions['is_sharable'])->with('workflow')
            ->with('formUnit', function ($query) use ($conditions) {
                return isset($conditions['unit_ids']) ?
                $query->whereIn('unit_id', $conditions['unit_ids']) :
                $query;
            });

        return isset($conditions['name']) ? $form->where('name', 'like', '%'.$conditions['name'].'%')->paginate($conditions['per_page']) :
            $form->paginate($conditions['per_page']);
    }

    public function updateForm(array $formRequest, Form $form, array $questionType): Form
    {
        $questionTypeJson = json_encode($questionType);
        $formRequest['questions'] = $questionTypeJson === false ? json_encode([]) : $questionTypeJson;

        $formRequest['origin_form_id'] = $form->id;

        $isWritableJson = json_encode($form->is_writable);
        $formRequest['is_writable'] = $isWritableJson === false ? json_encode([]) : $isWritableJson;
        $formRequest['version'] = $form->version + 1;

        return $this->create($formRequest);
    }

    public function storeFormCopy(Form $form, array $request): Form
    {
        $newForm = new Form();
        $newForm->name = ! isset($request['name']) ? '自訂表單'.date('Y-m-d H:i') : $request['name'];
        $newForm->origin_form_id = null;
        $newForm->is_sharable = $request['is_sharable'];

        //TODO:check tmu
        $newForm->type = $form->type;
        $newForm->questions = $form->questions;
        $newForm->reviewed = $form->reviewed;
        $newForm->is_writable = $form->is_writable;
        $newForm->form_default_workflow = $form->form_default_workflow;
        $newForm->course_form_default_assessment = $form->course_form_default_assessment;
        $newForm->save();

        return $newForm;
    }

    public function batchUpdateFormReviewed(array $form_ids, int $reviewed): Collection
    {
        return collect($form_ids)->map(function ($formId) use ($reviewed) {
            $form = $this->getReviewedForm($formId);
            if (! is_null($form)) {
                $form->reviewed = $reviewed === ReviewedEnum::PASS ?
                 ReviewedEnum::PASS : ReviewedEnum::REFUSE;
                $form->update();

                return $formId;
            }
        });
    }

    public function updateFormEnable(int $id, bool $is_enabled): void
    {
        $form = $this->getById($id);
        $form->is_enabled = $is_enabled;
        $form->update();
    }

    public function updateFormBaseSetting(int $id, array $conditions): Form
    {
        $form = $this->getById($id);
        $form->name = $conditions['name'];
        $form->is_sharable = $conditions['is_sharable'];
        $form->type = $conditions['type'];
        $form->is_writable = $conditions['is_writable'];
        $form->reviewed = $conditions['reviewed'];
        $form->update();

        return $form;
    }

    private function checkFormUnitId(array $condition): EloquentBuilder
    {
        return ! isset($condition['unit_ids']) ? Form::with('formUnit') :
        Form::whereHas('formUnit', function ($query) use ($condition) {
            return $query->whereIn('unit_id', $condition['unit_ids']);
        })->with('formUnit');
    }
}
