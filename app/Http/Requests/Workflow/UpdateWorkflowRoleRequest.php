<?php

namespace App\Http\Requests\Workflow;

use App\Http\Requests\BaseRequest;

class UpdateWorkflowRoleRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'role' => 'required|integer',
        ];
    }
}
