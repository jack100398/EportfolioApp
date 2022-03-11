<?php

namespace App\Http\Requests\Workflow;

use App\Http\Requests\BaseRequest;

class ThresholdWorkflowRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'thresholdFormIds' => 'required|array',
        ];
    }
}
