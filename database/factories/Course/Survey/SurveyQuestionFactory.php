<?php

namespace Database\Factories\Course\Survey;

use App\Models\Course\Survey\Survey;
use App\Models\Course\Survey\SurveyQuestion;
use Illuminate\Database\Eloquent\Factories\Factory;

class SurveyQuestionFactory extends Factory
{
    protected $model = SurveyQuestion::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'survey_id' => Survey::factory()->create()->id,
            'sort' => 1,
            'content' => $this->faker->name,
            'type' => $this->faker->numberBetween(1, 3),
            'metadata' => ['sour' => 1, 'comment' => 'option', 'score' => 60],
        ];
    }
}
