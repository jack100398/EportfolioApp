<?php

namespace Database\Factories\Workflow;

use App\Models\Auth\User;
use App\Models\Workflow\IgnoreThresholdForm;
use App\Models\Workflow\ThresholdForm;
use Illuminate\Database\Eloquent\Factories\Factory;

class IgnoreThresholdFormFactory extends Factory
{
    protected $model = IgnoreThresholdForm::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'origin_threshold_id' => ThresholdForm::factory()->create()->id,
            'user_id'=>User::factory()->create(['deleted_at'=>null])->id,
        ];
    }
}
