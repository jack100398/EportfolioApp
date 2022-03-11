<?php

namespace App\Http\Requests\Course;

use Illuminate\Foundation\Http\FormRequest;

class SearchCourseRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'year' => 'required|integer',
            'searchContent' => 'nullable|string',
            'unit_id' => 'required|integer',
            'course_mode' => 'required|array',
            'credit' => 'nullable|array',
            'start_at' => 'date',
            'end_at' => 'date',
            'assessment_id' => 'nullable|array',
        ];
    }
}
