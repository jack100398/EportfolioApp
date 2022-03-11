<?php

namespace Database\Factories\Course;

use App\Models\Course\AssessmentType;
use App\Models\Form\Form;
use App\Models\Model;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssessmentTypeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AssessmentType::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'type' => $this->faker->numberBetween(1, 4),
            'assessment_name' => $this->faker->name(),
            'unit_id' => Unit::factory()->create()->id,
            'source' => $this->createForm(),
        ];
    }

    private function createForm()
    {
        return Form::factory()->create()->id;
    }
}
