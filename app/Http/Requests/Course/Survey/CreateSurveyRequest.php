<?php

namespace App\Http\Requests\Course\Survey;

use Illuminate\Foundation\Http\FormRequest;

class CreateSurveyRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string',
            'public' => 'required|boolean',
            'origin' => 'integer',
            'unit_id' => 'required|exists:units,id',
        ];
    }
}
