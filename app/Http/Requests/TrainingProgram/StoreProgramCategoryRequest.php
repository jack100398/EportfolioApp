<?php

namespace App\Http\Requests\TrainingProgram;

use App\Http\Requests\BaseRequest;

class StoreProgramCategoryRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'parent_id' => 'nullable|exists:training_program_categories,id',
            'training_program_id' => 'required|exists:training_programs,id',
            'unit_id' => 'required|exists:units,id',
            'default_category_id' => 'nullable|exists:default_categories,id',
            'is_training_item' => 'required|boolean',
            'name' => 'required',
            'sort' => 'required',
            'created_by' => 'exclude',
        ];
    }
}
