<?php

namespace App\Http\Requests\TrainingProgram;

use App\Http\Requests\BaseRequest;

class StoreProgramUserRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'training_program_id' => 'required|exists:training_programs,id',
            'user_id' => 'required|exists:users,id',
            'phone_number' => '',
            'group_name' => '',
        ];
    }
}
