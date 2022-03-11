<?php

namespace App\Services\Workflow;

use App\Models\Auth\User;
use App\Models\Workflow\IgnoreThresholdForm;
use App\Services\Interfaces\IIgnoreThresholdFormService;
use Illuminate\Support\Collection;

class IgnoreThresholdFormService implements IIgnoreThresholdFormService
{
    public function getById(int $id): IgnoreThresholdForm
    {
        return IgnoreThresholdForm::findOrFail($id);
    }

    public function getByUserIdAndOriginThresholdId(int $userId, int $originThresholdId): IgnoreThresholdForm
    {
        return IgnoreThresholdForm::where([['user_id', $userId],
            ['origin_threshold_id', $originThresholdId],
        ])->firstOrFail();
    }

    public function getByUserIdAndOriginThresholdIds(int $userId, array $originThresholdIds): Collection
    {
        return IgnoreThresholdForm::where('user_id', $userId)
            ->whereIn('origin_threshold_id', $originThresholdIds)->get();
    }

    public function store(array $conditions): IgnoreThresholdForm
    {
        User::findOrFail($conditions['user_id']);
        $ignoreThreshold = new IgnoreThresholdForm();
        $ignoreThreshold->user_id = $conditions['user_id'];
        $ignoreThreshold->origin_threshold_id = $conditions['origin_threshold_id'];
        $ignoreThreshold->save();

        return $ignoreThreshold;
    }

    public function deleteById(int $id): bool
    {
        return $this->getById($id)->delete() === true;
    }
}
