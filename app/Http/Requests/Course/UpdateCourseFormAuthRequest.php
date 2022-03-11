<?php

namespace App\Http\Requests\Course;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCourseFormAuthRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'source' => 'required|integer',
        ];
    }
}
