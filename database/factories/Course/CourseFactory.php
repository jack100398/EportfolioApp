<?php

namespace Database\Factories\Course;

use App\Models\Auth\User;
use App\Models\Course\Course;
use App\Models\Course\CourseTarget;
use App\Models\Course\Credit;
use App\Models\DefaultCategory;
use App\Models\TrainingProgram\TrainingProgramCategory;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Course::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'year'=> $this->faker->numberBetween(108, 111),
            'program_category_id'=> TrainingProgramCategory::factory()->create()->id,
            'default_category_id'=> DefaultCategory::factory()->create()->id,
            'course_target' => CourseTarget::factory()->create()->id,
            'unit_id'=> Unit::factory()->create()->id,
            'course_name'=> $this->faker->name(),
            'course_remark'=> $this->faker->name(),
            'open_signup_for_student'=> $this->faker->boolean(),
            'place'=> $this->faker->name(),
            'course_mode'=> $this->faker->numberBetween(1, 30),
            'is_compulsory'=> $this->faker->boolean(),
            'auto_update_students'=> $this->faker->boolean(),
            'created_by'=> $this->getUserId(),
            'updated_by'=> $this->getUserId(),
            'is_notified'=>$this->faker->boolean(),
            'metadata'=>['continuing_credit' => Credit::factory()->create()->id, 'hospital_credit'=>Credit::factory()->create()->id, 'people_limit' => 0],
        ];
    }

    private function getUserId(): int
    {
        return User::factory()->create()->id;
    }
}
