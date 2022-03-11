<?php

namespace App\Http\Requests\Form;

use App\Services\Form\Enum\FormTypeEnum;
use App\Services\Form\Enum\IsSharableEnum;
use App\Services\Form\Enum\IsWritableEnum;
use App\Services\Form\Enum\ReviewedEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFormRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'unit_ids' => 'required|array',
            'name' => 'string',
            'type' => ['required', Rule::in(FormTypeEnum::TYPES),
            ],
            'reviewed' => ['required', Rule::in([
                ReviewedEnum::EDIT,
                ReviewedEnum::UNAPPROVED,
            ]),
            ],
            'is_writable' => ['array', Rule::in(IsWritableEnum::TYPES),
            ],
            'is_sharable' => ['required', Rule::in(IsSharableEnum::TYPES),
            ],
            'questions' => 'required|array',
            'form_default_workflow' => 'required|array',
            'course_form_default_assessment' => 'required|integer',
        ];
    }
}
