<?php

namespace App\Http\Requests\Workflow;

use App\Http\Requests\BaseRequest;

class UpdateBatchModifyProcessRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'old_sign_by' => 'required|int',
            'ids' => 'required|array',
            'new_sign_by' => 'required|int',
        ];
    }
}
