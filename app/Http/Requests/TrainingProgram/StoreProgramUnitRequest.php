<?php

namespace App\Http\Requests\TrainingProgram;

use App\Http\Requests\BaseRequest;

class StoreProgramUnitRequest extends BaseRequest
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
            'unit_id' => 'required|exists:units,id',
        ];
    }
}
