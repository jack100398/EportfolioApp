<?php

namespace Database\Factories\Course;

use App\Models\Course\CoursePlace;
use Illuminate\Database\Eloquent\Factories\Factory;

class CoursePlaceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CoursePlace::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'parent_id' => $this->faker->boolean(50) ? $this->MakeCoursePlace()->id : null,
            'name'=> $this->faker->name(),
        ];
    }

    private function MakeCoursePlace(): CoursePlace
    {
        $coursePlace = CoursePlace::factory()->make();
        $coursePlace->save();

        return $coursePlace;
    }
}
