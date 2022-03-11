<?php

namespace App\Http\Requests\Course;

use Illuminate\Foundation\Http\FormRequest;

class CreateCourseMaterialRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'course_id' => 'required|int',
            'material_id' => 'required|int',
            'description' => 'required|string',
            'required_time' => 'time',
            'opened_at' => 'date',
            'ended_at' => 'date',
        ];
    }
}
