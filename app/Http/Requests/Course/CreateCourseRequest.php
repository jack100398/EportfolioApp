<?php

namespace App\Http\Requests\Course;

use Illuminate\Foundation\Http\FormRequest;

class CreateCourseRequest extends FormRequest
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
            'course_name' => 'required|string',
            'program_category_id' => 'required:integer',
            'unit_id' => 'required|integer',
            'place' => 'required|string',
            'assessment' => 'array',
            'course_remark' => 'string',
            'teachers' => 'required|array',
            'start_at' => 'required|date',
            'end_at' => 'required|date',
            'signup_start_at' => 'required|date',
            'signup_end_at' => 'required|date',
            'course_form_send_at' => 'required|date',
            'auto_update_students' => 'required|bool',
            'open_signup_for_student' => 'required|bool',
            'is_compulsory' => 'required|bool',
            'course_mode' => 'required|integer',
            'is_notified' => 'required|bool',
            'course_target' => 'integer',
            'people_limit' => 'integer',
            'metadata' => 'array',
            'combine_course' => 'integer',
            'other_teacher' => 'string',
            'continuing_credit' => 'integer',
            'hospital_credit' => 'integer',
            'students' => 'array',
            'overdue_type' => 'integer',
            'overdue_description' => 'string',

        ];
    }
}
