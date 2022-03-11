<?php

namespace Database\Factories\Course;

use App\Models\Auth\User;
use App\Models\Course\Course;
use App\Models\Course\CourseAssessment;
use App\Models\Course\CourseStudentAssessment;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseStudentAssessmentFactory extends Factory
{
    protected $model = CourseStudentAssessment::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'course_id' => Course::factory()->create()->id,
            'course_assessment_id' => CourseAssessment::factory()->create()->id,
            'student_id' => User::factory()->create()->id,
            'state' => 0,
            'is_teacher_process' => false,
            'is_student_process' => true,
            'is_direct_pass' => false,
        ];
    }
}
