<?php

namespace App\Http\Requests\Exam;

use App\Http\Requests\BaseRequest;

class StoreExamResultRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'exam_id' => 'required|exists:exams,id',
            'user_id' => 'required|exists:users,id',
            'start_time' => 'date',
            'end_time' => 'date',
        ];
    }
}
