<?php

namespace App\Http\Requests\Workflow;

use App\Http\Requests\BaseRequest;

class ThresholdFormListRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'programCategoryId' => 'required|integer',
        ];
    }
}
