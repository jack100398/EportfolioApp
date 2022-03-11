<?php

namespace App\Http\Requests\Course;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCourseRequest extends FormRequest
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
            'course_name' => 'required',
            'program_category_id' => 'required',
            'unit_id' => 'required',
            'course_remark' => 'required',
            'auto_update_students' => 'required',
            'open_signup_for_student' => 'required',
            'metadata' => 'required',
            'is_compulsory' => 'required',
            'course_mode' => 'required',
            'is_notified' => 'required',
            'overdue_type' => 'integer',
            'overdue_description' => 'string',
        ];
    }
}
