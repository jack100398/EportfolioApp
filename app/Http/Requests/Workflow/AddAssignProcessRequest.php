<?php

namespace App\Http\Requests\Workflow;

use App\Http\Requests\BaseRequest;
use App\Services\Workflow\Enum\ProcessTypeEnum;
use Illuminate\Validation\Rule;

class AddAssignProcessRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'process_id' => 'required|integer',
            'sign_by' => 'required|integer',
            'type' => Rule::in(ProcessTypeEnum::TYPES),
            'role' => 'required|integer',
        ];
    }
}
