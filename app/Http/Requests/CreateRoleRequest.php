<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateRoleRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|unique:roles',
            'readable_name' => 'required',
            'description' => '',
        ];
    }
}
