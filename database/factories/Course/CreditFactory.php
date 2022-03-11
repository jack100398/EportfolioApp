<?php

namespace Database\Factories\Course;

use App\Models\Course\Credit;
use Illuminate\Database\Eloquent\Factories\Factory;

class CreditFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Credit::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'year' => $this->faker->numberBetween(1, 255),
            'sort' => $this->faker->numberBetween(1, 255),
            'parent_id' => $this->faker->boolean(50) ? $this->MakeParentCredit()->id : null,
            'credit_name' => $this->faker->name(),
            'credit_type' => $this->faker->numberBetween(1, 2),
            'training_time' => [1 => 123, 2=>456],
        ];
    }

    private function MakeParentCredit(): Credit
    {
        return Credit::factory()->create(['parent_id' => null]);
    }
}
