<?php

namespace Database\Factories\Workflow;

use App\Models\Form\Form;
use App\Models\TrainingProgram\TrainingProgramCategory;
use App\Models\Workflow\DefaultWorkflow;
use App\Models\Workflow\ThresholdForm;
use Illuminate\Database\Eloquent\Factories\Factory;

class ThresholdFormFactory extends Factory
{
    protected $model = ThresholdForm::class;

    public function definition()
    {
        return [
            'program_category_id' => TrainingProgramCategory::factory()->create()->id,
            'default_workflow_id' => DefaultWorkflow::factory()->create()->id,
            'form_id' => Form::factory()->create()->id,
            'send_amount' => $this->faker->numberBetween(1, 5),
            'form_start_at' => $this->faker->numberBetween(1, 3),
            'form_write_at' => $this->faker->numberBetween(1, 3),
        ];
    }
}
