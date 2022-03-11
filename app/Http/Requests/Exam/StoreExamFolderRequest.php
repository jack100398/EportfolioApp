<?php

namespace App\Http\Requests\Exam;

use App\Http\Requests\BaseRequest;
use App\Models\Exam\ExamFolder;
use Illuminate\Validation\Rule;

class StoreExamFolderRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => 'required',
            'parent_id' => 'nullable|exists:exam_folders,id',
            'type' => [
                'required',
                Rule::in(ExamFolder::TYPES),
            ],
        ];
    }
}
