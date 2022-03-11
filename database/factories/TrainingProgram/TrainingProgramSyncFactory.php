<?php

namespace Database\Factories\TrainingProgram;

use App\Models\TrainingProgram\TrainingProgram;
use App\Models\TrainingProgram\TrainingProgramSync;
use App\Models\TrainingProgram\TrainingProgramUnit;
use App\Models\TrainingProgram\TrainingProgramUser;
use DateInterval;
use Illuminate\Database\Eloquent\Factories\Factory;

class TrainingProgramSyncFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TrainingProgramSync::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'from_training_program_id' => TrainingProgram::factory()->create()->id,
            'to_training_program_id' => TrainingProgram::factory()->create()->id,
        ];
    }
}
