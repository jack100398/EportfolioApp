<?php

namespace App\Http\Requests;

class UpdateUserRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'name' => 'bail|required|min:2',
            'email' => 'required|email'.$this->route('id'),
            'roles' => 'required|min:1',
        ];
    }
}
