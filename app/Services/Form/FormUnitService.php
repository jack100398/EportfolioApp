<?php

namespace App\Services\Form;

use App\Models\Form\FormUnit;
use App\Services\Form\Interfaces\IFormUnitService;
use Illuminate\Database\Eloquent\Collection;

class FormUnitService implements IFormUnitService
{
    public function getByFormId(int $formId): Collection
    {
        return FormUnit::where('form_id', $formId)->orderBy('id', 'desc')->get();
    }

    public function getByUnitId(int $unitId): Collection
    {
        return FormUnit::where('unit_id', $unitId)
            ->orderBy('form_id', 'desc')
            ->get('form_id');
    }

    public function storeFormUnit(int $formId, array $unitIds): void
    {
        array_map(function ($unitId) use ($formId) {
            $formUnit = new FormUnit();
            $formUnit->form_id = $formId;
            $formUnit->unit_id = $unitId;
            $formUnit->save();
        }, $unitIds);
    }
}
