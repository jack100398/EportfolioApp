<?php

namespace App\Services\Form\Interfaces;

use Illuminate\Database\Eloquent\Collection;

interface IFormUnitService
{
    public function getByFormId(int $formId): Collection;

    public function storeFormUnit(int $formId, array $unitIds): void;
}
