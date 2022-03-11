<?php

namespace App\Http\Requests\Exam;

use App\Http\Requests\BaseRequest;
use App\Models\Exam\ExamQuestion;
use Illuminate\Validation\Rule;

class StoreExamQuestionRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'context' => 'required',
            'metadata' => 'required|array',
            'answer_detail' => '',
            'type' => [
                'required',
                Rule::in(ExamQuestion::TYPES),
            ],
        ];
    }
}
