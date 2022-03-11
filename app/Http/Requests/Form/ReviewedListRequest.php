<?php

namespace App\Http\Requests\Form;

use Illuminate\Foundation\Http\FormRequest;

class ReviewedListRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => 'string',
            'per_page' => 'required|integer',
        ];
    }
}
