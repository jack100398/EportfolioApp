<?php

namespace App\Http\Requests;

class CreateUserRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'name' => 'bail|required|min:2',
            'email' => 'required|email',
            'username' => 'required|unique:users,username',
            'password' => 'required|min:6',
            'roles' => 'required|min:1',
        ];
    }
}
