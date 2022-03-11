<?php

namespace App\Services\Workflow;

use App\Models\Workflow\ManualForm;
use App\Services\Interfaces\IManualFormService;
use Illuminate\Support\Collection;

class ManualFormService implements IManualFormService
{
    public function getById(int $id): ManualForm
    {
        return ManualForm::findOrFail($id);
    }

    public function getByProgramId(int $programId): Collection
    {
        return ManualForm::where('training_program_id', $programId)->get();
    }

    public function store(array $conditions): ManualForm
    {
        $manualForm = new ManualForm();
        $manualForm->title = $conditions['title'];
        $manualForm->form_id = $conditions['form_id'];
        $manualForm->default_workflow_id = $conditions['default_workflow_id'];
        $manualForm->training_program_id = $conditions['training_program_id'];
        $manualForm->send_amount = $conditions['send_amount'];
        $manualForm->form_start_at = $conditions['form_start_at'];
        $manualForm->form_write_at = $conditions['form_write_at'];
        $manualForm->save();

        return $manualForm;
    }

    public function deleteById(int $id): bool
    {
        return ManualForm::findOrFail($id)->delete() === true;
    }
}
