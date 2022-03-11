<?php

namespace App\Http\Requests\Course\Survey;

use Illuminate\Foundation\Http\FormRequest;

class CreateCourseSurveyRecordRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'course_survey_id' => 'required|integer',
            'role_type' => 'required|integer',
            'metadata' => 'required|array',
        ];
    }
}
