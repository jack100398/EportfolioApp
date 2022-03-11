<?php

namespace App\Http\Requests\Form;

use App\Http\Requests\BaseRequest;
use App\Services\Form\Enum\IsSharableEnum;
use Illuminate\Validation\Rule;

class FormListRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'is_sharable' => ['required', Rule::in(IsSharableEnum::TYPES),
            ],
            'unit_ids' => 'array',
            'name' => 'string',
            'per_page' => 'required|integer',
        ];
    }
}
