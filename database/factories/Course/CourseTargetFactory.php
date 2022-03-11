<?php

namespace Database\Factories\Course;

use App\Models\Course\CourseTarget;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseTargetFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CourseTarget::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'target_name' => $this->faker->name(),
            'sort' => $this->faker->numberBetween(1, 5),
            'viewable'=>$this->faker->boolean(),
        ];
    }
}
