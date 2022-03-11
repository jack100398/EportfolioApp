<?php

namespace App\Http\Requests\Course\Survey;

use Illuminate\Foundation\Http\FormRequest;

class CreateSurveyQuestionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'survey_id' => 'required|integer',
            'content' => 'required|string',
            'sort' => 'required|integer',
            'type' => 'required|integer',
            'option_content' => 'array',
            'option_score' => 'array',
        ];
    }
}
