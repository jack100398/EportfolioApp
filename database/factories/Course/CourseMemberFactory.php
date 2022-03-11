<?php

namespace Database\Factories\Course;

use App\Models\Auth\User;
use App\Models\Course\Course;
use App\Models\Course\CourseMember;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseMemberFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CourseMember::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'course_id' => $this->MakeCourse()->id,
            'user_id'=> $this->getUserId(),
            'role'=> $this->faker->numberBetween(1, 4),
            'is_online_course'=> $this->faker->boolean(),
            'updated_by'=> $this->getUserId(),
            'state'=>$this->faker->boolean(),
        ];
    }

    private function getUserId(): int
    {
        return User::factory()->create()->id;
    }

    private function MakeCourse()
    {
        return Course::factory()->create();
    }
}
