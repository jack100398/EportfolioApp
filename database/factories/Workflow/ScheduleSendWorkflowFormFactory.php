<?php

namespace Database\Factories\Workflow;

use App\Models\Auth\User;
use App\Models\Workflow\ThresholdForm;
use App\Models\Unit;
use App\Models\Workflow\ScheduleSendWorkflowForm;
use App\Services\Workflow\Enum\WorkflowTypeEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

class ScheduleSendWorkflowFormFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ScheduleSendWorkflowForm::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'key_id'=>ThresholdForm::factory()->create()->id,
            'title'=> $this->faker->title,
            'unit_id'=>Unit::factory()->create()->id,
            'type'=>WorkflowTypeEnum::THRESHOLD,
            'start_at'=>date('Y-m-d'),
            'end_at'=>date('Y-m-d'),
            'create_at'=>1,
            'student_id'=>User::factory()->create(['deleted_at'=>null])->id,
        ];
    }
}
