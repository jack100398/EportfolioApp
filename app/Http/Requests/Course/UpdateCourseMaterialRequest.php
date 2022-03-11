<?php

namespace App\Http\Requests\Course;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCourseMaterialRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'material_id' => 'required|int',
            'description' => 'required|string',
            'required_time' => 'required|int',
            'opened_at' => 'nullable|date',
            'ended_at' => 'nullable|date',
        ];
    }
}
