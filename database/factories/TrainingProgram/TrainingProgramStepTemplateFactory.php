<?php

namespace Database\Factories\TrainingProgram;

use App\Models\TrainingProgram\TrainingProgram;
use App\Models\TrainingProgram\TrainingProgramStep;
use App\Models\TrainingProgram\TrainingProgramStepTemplate;
use App\Models\TrainingProgram\TrainingProgramUnit;
use App\Models\TrainingProgram\TrainingProgramUser;
use DateInterval;
use Illuminate\Database\Eloquent\Factories\Factory;

class TrainingProgramStepTemplateFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TrainingProgramStepTemplate::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'training_program_id' => TrainingProgram::factory()->create()->id,
            'program_unit_id' => TrainingProgramUnit::factory()->create()->id,
            'days' => $this->faker->numberBetween(1, 60),
            'sequence' => $this->faker->numberBetween(0, 10),
        ];
    }
}
