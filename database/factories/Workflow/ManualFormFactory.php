<?php

namespace Database\Factories\Workflow;

use App\Models\Form\Form;
use App\Models\TrainingProgram\TrainingProgram;
use App\Models\Workflow\DefaultWorkflow;
use App\Models\Workflow\ManualForm;
use Illuminate\Database\Eloquent\Factories\Factory;

class ManualFormFactory extends Factory
{
    protected $model = ManualForm::class;

    public function definition()
    {
        return [
            'title' => $this->faker->title,
            'training_program_id' => TrainingProgram::factory()->create()->id,
            'default_workflow_id' => DefaultWorkflow::factory()->create()->id,
            'form_id' => Form::factory()->create()->id,
            'send_amount' => $this->faker->numberBetween(1, 5),
            'form_start_at' => $this->faker->numberBetween(1, 3),
            'form_write_at' => $this->faker->numberBetween(1, 3),
        ];
    }
}
