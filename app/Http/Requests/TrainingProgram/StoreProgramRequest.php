<?php

namespace App\Http\Requests\TrainingProgram;

use App\Http\Requests\BaseRequest;

class StoreProgramRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'year' => 'required',
            'unit_id' => 'required|exists:units,id',
            'occupational_class_id' => 'nullable|exists:occupational_classes,id',
            'name' => 'required',
            'start_date' => 'required',
            'end_date' => 'required|after_or_equal:start_date',
        ];
    }
}
