<?php

namespace Database\Factories\Course\Survey;

use App\Models\Auth\User;
use App\Models\Course\Survey\CourseSurvey;
use App\Models\Course\Survey\CourseSurveyRecord;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseSurveyRecordFactory extends Factory
{
    protected $model = CourseSurveyRecord::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'answered_by' => User::factory()->create()->id,
            'course_survey_id' => CourseSurvey::factory()->create()->id,
            'role_type' => 1,
            'metadata' => [1, 1, 1],
        ];
    }
}
