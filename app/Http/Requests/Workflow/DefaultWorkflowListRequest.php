<?php

namespace App\Http\Requests\Workflow;

use Illuminate\Foundation\Http\FormRequest;

class DefaultWorkflowListRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => 'string',
            'per_page' => 'required|integer',
        ];
    }
}
