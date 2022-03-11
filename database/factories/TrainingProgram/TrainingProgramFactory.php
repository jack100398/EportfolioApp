<?php

namespace Database\Factories\TrainingProgram;

use App\Models\TrainingProgram\OccupationalClass;
use App\Models\TrainingProgram\TrainingProgram;
use App\Models\Unit;
use DateInterval;
use Illuminate\Database\Eloquent\Factories\Factory;

class TrainingProgramFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TrainingProgram::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $startDate = $this->faker->dateTimeBetween('-1 years', '+1 years');
        $endDate = $this->faker->dateTimeBetween(
            (clone $startDate)->add(new DateInterval('P1D')),
            (clone $startDate)->add(new DateInterval('P1Y'))
        );

        return [
            'year' => $this->faker->numberBetween(90, 120),
            'unit_id' =>  Unit::factory()->create()->id,
            'occupational_class_id' => OccupationalClass::factory()->create()->id,
            'name' => $this->faker->company().' '.$this->faker->randomNumber(3),
            'start_date' => $startDate,
            'end_date' => $endDate,
        ];
    }
}
