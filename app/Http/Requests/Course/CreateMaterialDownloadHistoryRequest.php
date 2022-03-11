<?php

namespace App\Http\Requests\Course;

use Illuminate\Foundation\Http\FormRequest;

class CreateMaterialDownloadHistoryRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'course_material_id' => 'required|integer',
            'opened_counts' => 'required|integer',
            'downloaded_counts' => 'required|integer',
            'reading_time' => 'required',
        ];
    }
}
