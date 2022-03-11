<?php

namespace Database\Factories\TrainingProgram;

use App\Models\TrainingProgram\OccupationalClass;
use Illuminate\Database\Eloquent\Factories\Factory;

class OccupationalClassFactory extends Factory
{
    protected $model = OccupationalClass::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->company(),
            'parent_id' => null,
        ];
    }
}
