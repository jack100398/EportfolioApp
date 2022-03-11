<?php

namespace App\Http\Requests\Form;

use App\Http\Requests\BaseRequest;
use App\Services\Form\Enum\FormWriteRecordFlagEnum;
use Illuminate\Validation\Rule;

class CreateWriteRecordRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'process_id' => 'required|integer',
            'user_id' => 'required|exists:users,id',
            'result' => 'required|array',
            'flag' => ['required', Rule::in(FormWriteRecordFlagEnum::TYPES)],
        ];
    }
}
