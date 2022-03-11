<?php

namespace App\Http\Requests\TrainingProgram;

use App\Http\Requests\BaseRequest;

class StoreProgramStepRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'program_unit_id' => 'nullable|exists:training_program_units,id',
            'program_user_id' => 'required|exists:training_program_users,id',
            'name' => 'required_without:program_unit_id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:end_date',
            'remarks' => '',
        ];
    }
}
