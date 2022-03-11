<?php

namespace Database\Factories\TrainingProgram;

use App\Models\TrainingProgram\TrainingProgramStep;
use App\Models\TrainingProgram\TrainingProgramUnit;
use App\Models\TrainingProgram\TrainingProgramUser;
use DateInterval;
use Illuminate\Database\Eloquent\Factories\Factory;

class TrainingProgramStepFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TrainingProgramStep::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $startDate = $this->faker->dateTimeBetween('-1 months', '+1 months');
        $endDate = $this->faker->dateTimeBetween(
            (clone $startDate)->add(new DateInterval('P1D')),
            (clone $startDate)->add(new DateInterval('P1M'))
        );

        return [
            'program_unit_id' => TrainingProgramUnit::factory()->create()->id,
            'program_user_id' => TrainingProgramUser::factory()->create()->id,
            'name' => $this->faker->company(),
            'start_date' =>$startDate,
            'end_date'=>$endDate,
            'remarks' => $this->faker->word(10),
        ];
    }
}
