<?php

namespace Database\Factories\Exam;

use App\Models\Auth\User;
use App\Models\Course\Course;
use App\Models\Exam\Exam;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExamFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Exam::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title'               => $this->faker->text(20),
            'description'         => $this->faker->text(40),
            'invigilator'         => $this->faker->name(),
            'start_time'          => $this->faker->dateTime(),
            'end_time'            => $this->faker->dateTime(),
            'original_start_time' => $this->faker->dateTime(),
            'original_end_time'   => $this->faker->dateTime(),
            'is_answer_visible'   => $this->faker->boolean(),
            'scoring'             => $this->faker->numberBetween(0, 2),
            'passed_score'        => $this->faker->numberBetween(4, 10) * 10,
            'total_score'         => $this->faker->numberBetween(10, 20) * 10,
            'question_type'       => $this->faker->numberBetween(0, 3),
            'random_parameter'    => [],
            'limit_times'         => $this->faker->numberBetween(0, 10),
            'answer_time'         => $this->faker->time(),
            'created_by'          => User::factory()->create()->id,
            'is_template'         => $this->faker->boolean(),
            'course_id'           => Course::factory()->create()->id,
        ];
    }
}
