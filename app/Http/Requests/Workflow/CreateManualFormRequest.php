<?php

namespace App\Http\Requests\Workflow;

use App\Http\Requests\BaseRequest;

class CreateManualFormRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'title' => 'required|string',
            'training_program_id' => 'required|integer',
            'default_workflow_id' => 'required|integer',
            'form_id' => 'required|integer',
            'send_amount' => 'required|integer',
            'form_start_at' => 'required|integer',
            'form_write_at' => 'required|integer',
        ];
    }
}
