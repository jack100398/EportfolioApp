<?php

namespace App\Http\Requests\TrainingProgram;

use App\Http\Requests\BaseRequest;

class StoreAttachmentRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'training_program_id' => 'nullable|exists:training_programs,id',
            'file_id' => 'nullable|exists:files,id',
            'url' => '',
        ];
    }
}
