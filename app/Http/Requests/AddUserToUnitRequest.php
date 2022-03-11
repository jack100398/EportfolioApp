<?php

namespace App\Http\Requests;

use App\Services\UnitUserEnum;
use Illuminate\Validation\Rule;

class AddUserToUnitRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'unit_id' => 'required|exists:units,id',
            'user_id' => 'required|exists:users,id',
            'type' => [
                'required',
                Rule::in(UnitUserEnum::TYPES),
            ],
        ];
    }
}
