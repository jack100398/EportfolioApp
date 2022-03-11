<?php

namespace App\Http\Requests\Course\Survey;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCourseSurveyRecordRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'metadata' => 'required|array',
        ];
    }
}
