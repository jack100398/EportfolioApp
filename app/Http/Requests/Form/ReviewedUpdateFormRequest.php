<?php

namespace App\Http\Requests\Form;

use App\Services\Form\Enum\ReviewedEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReviewedUpdateFormRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'form_ids' => 'required|array',
            'reviewed' => ['required', Rule::in([
                ReviewedEnum::EDIT,
                ReviewedEnum::UNAPPROVED,
            ]),
            ],
        ];
    }
}
