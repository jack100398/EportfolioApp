<?php

namespace App\Http\Requests\Form;

use App\Services\Form\Enum\IsSharableEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FormCopyRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'form_id' => 'required|integer',
            'name' => 'required|string',
            'unit_ids' => 'required|array',
            'is_sharable' => ['required', Rule::in(IsSharableEnum::TYPES),
            ],
        ];
    }
}
