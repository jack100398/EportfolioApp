<?php

namespace App\Http\Requests;

class StoreDefaultCategoryRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'parent_id' => 'nullable|exists:default_categories,id',
            'school_year' => 'required',
            'unit_id' => 'nullable|exists:units,id',
            'name' => 'required',
        ];
    }
}
