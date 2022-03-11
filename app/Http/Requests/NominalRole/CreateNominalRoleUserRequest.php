<?php

namespace App\Http\Requests\NominalRole;

use App\Http\Requests\BaseRequest;

class CreateNominalRoleUserRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'nominal_role_id' => 'required|exists:nominal_roles,id',
            'morph_id' => 'required',
        ];
    }
}
