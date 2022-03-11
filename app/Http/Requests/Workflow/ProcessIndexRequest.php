<?php

namespace App\Http\Requests\Workflow;

use App\Http\Requests\BaseRequest;

class ProcessIndexRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'workflow_id' => 'required|integer',
        ];
    }
}
