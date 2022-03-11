<?php

namespace App\Http\Requests\Workflow;

use App\Http\Requests\BaseRequest;

class UpdateProcessRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'opinion' => 'string',
        ];
    }
}
