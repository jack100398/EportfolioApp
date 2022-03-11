<?php

namespace App\Services\Interfaces;

use App\Models\Workflow\ManualForm;
use Illuminate\Support\Collection;

interface IManualFormService
{
    public function getById(int $id): ManualForm;

    public function getByProgramId(int $programId): Collection;

    public function store(array $conditions): ManualForm;

    public function deleteById(int $id): bool;
}
