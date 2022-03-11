<?php

namespace App\Http\Requests\Form;

use Illuminate\Foundation\Http\FormRequest;

class FormEnabledRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'is_enabled' => 'required|boolean',
        ];
    }
}
