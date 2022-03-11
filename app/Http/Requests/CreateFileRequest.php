<?php

namespace App\Http\Requests;

class CreateFileRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'file' => 'required',
            'directory' => 'required',
        ];
    }
}
