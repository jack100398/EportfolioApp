<?php

namespace Database\Factories\TrainingProgram;

use App\Models\TrainingProgram\TrainingProgram;
use App\Models\TrainingProgram\TrainingProgramUnit;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

class TrainingProgramUnitFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TrainingProgramUnit::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'training_program_id' => TrainingProgram::factory()->create()->id,
            'unit_id' => Unit::factory()->create()->id,
        ];
    }
}
