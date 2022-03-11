<?php

namespace App\Http\Requests\Course;

use Illuminate\Foundation\Http\FormRequest;

class CreateAssessmentTypeRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'type' => 'required|integer',
            'assessment_name' => 'required|string',
            'unit_id' => 'required|integer',
            'source' => 'required|integer',
        ];
    }
}
