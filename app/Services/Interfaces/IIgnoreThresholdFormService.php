<?php

namespace App\Services\Interfaces;

use App\Models\Workflow\IgnoreThresholdForm;
use Illuminate\Support\Collection;

interface IIgnoreThresholdFormService
{
    public function getById(int $id): IgnoreThresholdForm;

    public function getByUserIdAndOriginThresholdId(int $userId, int $originThresholdId): IgnoreThresholdForm;

    public function getByUserIdAndOriginThresholdIds(int $userId, array $originThresholdIds): Collection;

    public function store(array $conditions): IgnoreThresholdForm;

    public function deleteById(int $id): bool;
}
