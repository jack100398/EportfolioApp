<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class UpdateRoleRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                Rule::unique('roles')->ignore($this->id),
            ],
            'readable_name' => 'required',
            'description' => '',
        ];
    }
}
