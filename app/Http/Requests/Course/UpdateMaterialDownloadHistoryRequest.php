<?php

namespace App\Http\Requests\Course;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMaterialDownloadHistoryRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'opened_counts' => 'required|integer',
            'downloaded_counts' => 'required|integer',
            'reading_time' => 'required',
        ];
    }
}
