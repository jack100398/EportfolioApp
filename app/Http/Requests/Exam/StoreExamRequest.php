<?php

namespace App\Http\Requests\Exam;

use App\Http\Requests\BaseRequest;

class StoreExamRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'title' => 'required',
            'description' => 'nullable',
            'invigilator' => 'nullable',
            'start_time' => 'required|date',
            'end_time' => 'required|date',
            'is_answer_visible' => 'required|boolean',
            'scoring' => 'required',
            'passed_score' => 'required|numeric',
            'total_score' => 'required|numeric',
            'question_type' => 'required',
            'random_parameter' => 'nullable',
            'limit_times' => 'required|numeric',
            'answer_time' => 'required',
            'is_template' => 'required|boolean',
            'course_id' => 'integer',
        ];
    }
}
