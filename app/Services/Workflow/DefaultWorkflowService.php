<?php

namespace App\Services\Workflow;

use App\Models\NominalRole\NominalRole;
use App\Models\Workflow\DefaultWorkflow;
use App\Services\Interfaces\IDefaultWorkflowService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class DefaultWorkflowService implements IDefaultWorkflowService
{
    public function getByPagination(array $condition): LengthAwarePaginator
    {
        return isset($condition['title']) ?
            DefaultWorkflow::where('title', 'like', '%'.$condition['title'])
                ->orderByDesc('id')->paginate($condition['per_page']) :
            DefaultWorkflow::orderByDesc('id')->paginate($condition['per_page']);
    }

    public function getById(int $id): DefaultWorkflow
    {
        return DefaultWorkflow::findOrFail($id);
    }

    public function getByIds(array $ids): Collection
    {
        return DefaultWorkflow::whereIn('id', $ids)->get();
    }

    public function update(DefaultWorkflow $defaultWorkflow, array $data): bool
    {
        $defaultWorkflow->unit_id = $data['unit_id'];
        $defaultWorkflow->process = $this->mapProcess($data['processes'])->toJson();
        $defaultWorkflow->title = $data['title'];

        return $defaultWorkflow->update();
    }

    public function checkDefaultWorkflow(array $defaultWorkflows): bool
    {
        return $this->getByIds($defaultWorkflows)->count() === count($defaultWorkflows) ?
        true : false;
    }

    public function store(array $conditions): DefaultWorkflow
    {
        $defaultWorkflow = new DefaultWorkflow();
        $defaultWorkflow->unit_id = $conditions['unit_id'];
        $defaultWorkflow->title = $conditions['title'];
        $defaultWorkflow->process = $this->mapProcess($conditions['processes'])->toJson();
        $defaultWorkflow->save();

        return $defaultWorkflow;
    }

    public function deleteById(int $id): bool
    {
        return DefaultWorkflow::findOrFail($id)->delete() === true;
    }

    private function mapProcess(array $processes): Collection
    {
        return collect($processes)->map(function ($process) {
            return $process;
        })->filter(function ($process) {
            return ! is_null(NominalRole::find($process['role']));
        });
    }
}
