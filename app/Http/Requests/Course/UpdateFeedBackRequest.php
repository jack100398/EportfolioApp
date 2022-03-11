<?php

namespace App\Http\Requests\Course;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFeedBackRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'comment' => 'required|string',
            'public' => 'required|boolean',
            'usage' => 'required|integer',
        ];
    }
}
