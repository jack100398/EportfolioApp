<?php

namespace App\Http\Requests\Workflow;

use App\Http\Requests\BaseRequest;

class WorkflowIndexListRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'training_program_ids' => 'array',
            'unit_ids' => 'required|array',
            'start_at' => 'required|date_format:Y-m-d',
            'end_at' => 'required|date_format:Y-m-d',
            'form_ids' => 'array',
            'types' => 'required|array',
        ];
    }
}
