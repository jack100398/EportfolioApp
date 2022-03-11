<?php

namespace App\Http\Requests\Workflow;

use App\Http\Requests\BaseRequest;

class DisagreeProcessRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'process_id' => 'required|integer',
            'bacK_process_id' => 'required|integer',
            'opinion' => 'string',
        ];
    }
}
