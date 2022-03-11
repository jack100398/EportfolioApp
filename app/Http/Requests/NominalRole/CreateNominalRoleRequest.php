<?php

namespace App\Http\Requests\NominalRole;

use App\Http\Requests\BaseRequest;
use App\Models\NominalRole\NominalRole;
use Illuminate\Validation\Rule;

class CreateNominalRoleRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required',
            'type' => [
                'required',
                Rule::in(collect(NominalRole::TYPES)->keys()),
            ],
        ];
    }
}
