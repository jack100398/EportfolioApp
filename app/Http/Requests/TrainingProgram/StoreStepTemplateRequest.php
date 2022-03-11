<?php

namespace App\Http\Requests\TrainingProgram;

use App\Http\Requests\BaseRequest;

class StoreStepTemplateRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'training_program_id' => 'required|exists:training_programs,id',
            'program_unit_id' => 'required|exists:training_program_units,id',
            'days' => 'required|numeric',
            'sequence' => 'required|numeric',
        ];
    }
}
