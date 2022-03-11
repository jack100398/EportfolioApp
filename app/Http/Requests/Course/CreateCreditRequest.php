<?php

namespace App\Http\Requests\Course;

use Illuminate\Foundation\Http\FormRequest;

class CreateCreditRequest extends FormRequest
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
            'sort' => 'integer',
            'parent_id' => 'integer',
            'credit_name' => 'required|string',
            'credit_type' => 'required|integer',
            'training_time' => 'array',
        ];
    }
}
