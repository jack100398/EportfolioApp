<?php

namespace Database\Factories\Workflow;

use App\Models\Auth\User;
use App\Models\Form\Form;
use App\Models\Workflow\ThresholdForm;
use App\Models\TrainingProgram\TrainingProgram;
use App\Models\Unit;
use App\Models\Workflow\Workflow;
use App\Services\Workflow\Enum\WorkflowTypeEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

class WorkflowFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Workflow::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'evaluatee'=>User::factory()->create(['deleted_at'=>null])->id,
            'title'=>$this->faker->title,
            'training_program_id' => TrainingProgram::factory()->create()->id,
            'form_id'=>Form::factory()->create()->id,
            'unit_id'=>Unit::factory()->create()->id,
            'type'=>WorkflowTypeEnum::THRESHOLD,
            'data_id'=>ThresholdForm::factory()->create()->id,
            'create_by'=>User::factory()->create(['deleted_at'=>null])->id,
            'start_at'=>date('Y-m-d'),
            'end_at'=>date('Y-m-d'),
        ];
    }
}
