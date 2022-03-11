<?php

namespace App\Http\Requests\Course;

use Illuminate\Foundation\Http\FormRequest;

class CreateCourseMemberRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'course_id' => 'required|integer',
            'user_id' => 'required|integer',
            'is_online_course' => 'required|boolean',
            'role' => 'required|integer',
            'updated_by' => 'required|integer',
            'state' => 'required|boolean',
        ];
    }
}
