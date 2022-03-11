<?php

namespace App\Http\Requests\TrainingProgram;

use App\Http\Requests\BaseRequest;

class StoreOccupationalClassRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => 'required',
            'parent_id' => 'nullable|exists:occupational_classes,id',
        ];
    }
}
