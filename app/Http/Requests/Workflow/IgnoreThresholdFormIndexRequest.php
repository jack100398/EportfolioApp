<?php

namespace App\Http\Requests\Workflow;

use App\Http\Requests\BaseRequest;

class IgnoreThresholdFormIndexRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'user_id' => 'required|integer',
            'origin_threshold_ids' => 'required|array',
        ];
    }
}
