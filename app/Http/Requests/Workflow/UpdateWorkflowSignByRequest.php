<?php

namespace App\Http\Requests\Workflow;

use App\Http\Requests\BaseRequest;

class UpdateWorkflowSignByRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'sign_by' => 'required|integer',
        ];
    }
}
