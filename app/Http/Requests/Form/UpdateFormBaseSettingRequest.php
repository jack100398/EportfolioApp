<?php

namespace App\Http\Requests\Form;

use App\Services\Form\Enum\FormTypeEnum;
use App\Services\Form\Enum\IsSharableEnum;
use App\Services\Form\Enum\IsWritableEnum;
use App\Services\Form\Enum\ReviewedEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFormBaseSettingRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string',
            'is_sharable' => ['required', Rule::in([
                IsSharableEnum::NONE,
                IsSharableEnum::ALL,
                IsSharableEnum::UNIT,
            ]),
            ],
            'unit_ids' => 'required|array',
            'type' => ['required', Rule::in(FormTypeEnum::TYPES),
            ],
            'is_writable' => ['array', Rule::in([
                IsWritableEnum::STUDENT,
                IsWritableEnum::TEACHER,
            ]),
            ],
            'reviewed' => ['required', Rule::in([
                ReviewedEnum::EDIT,
                ReviewedEnum::UNAPPROVED,
            ]),
            ],
        ];
    }
}
