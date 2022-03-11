<?php

namespace Database\Factories\TrainingProgram\ModifiedRecord;

use App\Models\TrainingProgram\ModifiedRecord\TrainingProgramStepModifiedRecord;
use App\Models\TrainingProgram\TrainingProgramStep;
use App\Models\TrainingProgram\TrainingProgramUnit;
use App\Models\TrainingProgram\TrainingProgramUser;
use DateInterval;
use Illuminate\Database\Eloquent\Factories\Factory;

class TrainingProgramStepModifiedRecordFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TrainingProgramStepModifiedRecord::class;

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
            'action' => $this->faker->randomElement(TrainingProgramStepModifiedRecord::ACTIONS),
            'program_unit_id' => TrainingProgramUnit::factory()->create()->id,
            'program_user_id' => TrainingProgramUser::factory()->create()->id,
            'name' => $this->faker->company(),
            'start_date' =>$startDate,
            'end_date'=> $endDate,
            'created_by',
            'remarks' => $this->faker->word(10),
        ];
    }
}
