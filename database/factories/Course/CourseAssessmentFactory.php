<?php

namespace Database\Factories\Course;

use App\Models\Course\AssessmentType;
use App\Models\Course\Course;
use App\Models\Course\CourseAssessment;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseAssessmentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CourseAssessment::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $course = $this->MakeCourse();
        $assessmentType = $this->MakeAssessmentType();

        return [
            'course_id' => $course->id,
            'assessment_id' => $assessmentType->id,
            'data' => $this->faker->name,
        ];
    }

    private function MakeCourse(): Course
    {
        $course = Course::factory()->make();
        $course->save();

        return $course;
    }

    private function MakeAssessmentType(): AssessmentType
    {
        $assessmentType = AssessmentType::factory()->make();
        $assessmentType->save();

        return $assessmentType;
    }
}
