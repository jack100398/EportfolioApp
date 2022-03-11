<?php

namespace App\Http\Requests\Form;

use App\Http\Requests\BaseRequest;
use App\Services\Form\Enum\IsSharableEnum;
use Illuminate\Validation\Rule;

class FormSendListRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'unit_ids' => 'required|array',
            'is_sharable' => ['required', Rule::in(IsSharableEnum::TYPES)],
            'per_page' => 'required|int',
            'name' => 'string',
        ];
    }
}
