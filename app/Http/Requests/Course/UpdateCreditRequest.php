<?php

namespace App\Http\Requests\Course;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCreditRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'year' => 'integer',
            'sort' => 'integer',
            'parent_id' => 'integer',
            'credit_name' => 'string',
            'credit_type' => 'integer',
            'training_time' => 'array',
        ];
    }
}
