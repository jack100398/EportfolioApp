<?php

namespace App\Services\Workflow;

use App\Models\Workflow\ThresholdForm;
use App\Services\Interfaces\IThresholdFormService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ThresholdFormService implements IThresholdFormService
{
    public function getById(int $id): ThresholdForm
    {
        return ThresholdForm::findOrFail($id);
    }

    public function getByIds(array $ids): Collection
    {
        return ThresholdForm::whereIn('id', $ids)
            ->orderBy('id')->get();
    }

    public function getManyByPagination(int $per_page): LengthAwarePaginator
    {
        return ThresholdForm::orderByDesc('id')->paginate($per_page);
    }

    public function getByProgramCategoryId(int $programCategoryId): Collection
    {
        return ThresholdForm::where('program_category_id', $programCategoryId)->get();
    }

    public function updateThreshold(array $request, ThresholdForm $threshold): ThresholdForm
    {
        $threshold->delete();
        $newThreshold = new ThresholdForm();
        $newThreshold->form_id = $request['form_id'];
        $newThreshold->default_workflow_id = $request['default_workflow_id'];
        $newThreshold->program_category_id = $request['program_category_id'];
        $newThreshold->send_amount = $request['send_amount'];
        $newThreshold->form_start_at = $request['form_start_at'];
        $newThreshold->form_write_at = $request['form_write_at'];
        $newThreshold->origin_threshold_id = $threshold->origin_threshold_id === null ? $threshold->id : $threshold->origin_threshold_id;
        $newThreshold->save();

        return $newThreshold;
    }

    public function storeThreshold(array $request): ThresholdForm
    {
        $threshold = new ThresholdForm();
        $threshold->form_id = $request['form_id'];
        $threshold->default_workflow_id = $request['default_workflow_id'];
        $threshold->program_category_id = $request['program_category_id'];
        $threshold->send_amount = $request['send_amount'];
        $threshold->form_start_at = $request['form_start_at'];
        $threshold->form_write_at = $request['form_write_at'];
        $threshold->save();
        //TODO:新增加入表單排程

        return $threshold;
    }
}
