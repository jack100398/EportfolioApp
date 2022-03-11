<?php

namespace Database\Factories\Course;

use App\Models\Auth\User;
use App\Models\Course\CourseHistory;
use App\Models\DefaultCategory;
use App\Models\Model;
use App\Models\TrainingProgram\TrainingProgramCategory;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseHistoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CourseHistory::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'back_type' => $this->faker->numberBetween(1, 3),
            'request' => $this->faker->boolean(70) ? json_encode(['unit_id'=>123, 'test'=>'abc']) : json_encode([]),
            'course_id'=> $this->faker->numberBetween(1, 255),
            'year'=> $this->faker->numberBetween(108, 111),
            'program_category_id'=> TrainingProgramCategory::factory()->create()->id,
            'default_category_id'=> DefaultCategory::factory()->create()->id,
            'unit_id'=> Unit::factory()->create()->id,
            'course_name'=> $this->faker->name,
            'course_remark'=> $this->faker->name,
            'open_signup_for_student'=> $this->faker->boolean(),
            'place'=> $this->faker->name,
            'course_mode'=> $this->faker->numberBetween(1, 30),
            'is_compulsory'=> $this->faker->boolean(),
            'auto_update_students'=> $this->faker->boolean(),
            'created_by'=> User::factory()->create()->id,
            'updated_by'=> User::factory()->create()->id,
            'is_notified'=>$this->faker->boolean(),
            'metadata'=>$this->faker->name,
        ];
    }
}
