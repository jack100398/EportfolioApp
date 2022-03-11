<?php

namespace Database\Factories\Course\Survey;

use App\Models\Auth\User;
use App\Models\Course\Survey\CourseSurvey;
use App\Models\Course\Survey\Survey;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseSurveyFactory extends Factory
{
    protected $model = CourseSurvey::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'survey_id' => Survey::factory()->create()->id,
            'start_at' => now(),
            'end_at' => now()->addDays(10),
        ];
    }
}
