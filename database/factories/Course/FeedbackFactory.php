<?php

namespace Database\Factories\Course;

use App\Models\Auth\User;
use App\Models\Course\Feedback;
use Illuminate\Database\Eloquent\Factories\Factory;

class FeedbackFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Feedback::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'comment' => $this->faker->name,
            'public' => $this->faker->boolean(50),
            'usage' => $this->faker->boolean(50) ? 1 : 0,
            'created_by' => User::factory()->create()->id,
        ];
    }
}
