<?php

namespace App\Http\Requests\Workflow;

use Illuminate\Foundation\Http\FormRequest;

class CreateDefaultWorkflowRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'unit_id' => 'required|integer',
            'title' => 'required|string',
            'processes' => 'required|array',
        ];
    }
}
