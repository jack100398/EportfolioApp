<?php

namespace Database\Factories\Course\Survey;

use App\Models\Auth\User;
use App\Models\Course\Survey\Survey;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

class SurveyFactory extends Factory
{
    protected $model = Survey::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'created_by' => User::factory()->create()->id,
            'name' => $this->faker->name,
            'public' => $this->faker->boolean(),
            'version' => 0,
            'origin' => null,
            'unit_id' => Unit::factory()->create()->id,
        ];
    }
}
