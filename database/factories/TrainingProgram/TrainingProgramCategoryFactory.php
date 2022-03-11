<?php

namespace Database\Factories\TrainingProgram;

use App\Models\Auth\User;
use App\Models\DefaultCategory;
use App\Models\TrainingProgram\TrainingProgram;
use App\Models\TrainingProgram\TrainingProgramCategory;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

class TrainingProgramCategoryFactory extends Factory
{
    protected $model = TrainingProgramCategory::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'parent_id' => null,
            'training_program_id' => TrainingProgram::factory()->create()->id,
            'unit_id' => Unit::factory()->create()->id,
            'default_category_id' => DefaultCategory::factory()->create()->id,
            'is_training_item' => $this->faker->boolean(),
            'name' => $this->faker->company(),
            'sort' => $this->faker->numberBetween(0, 10),
            'created_by' => User::factory()->create()->id,
        ];
    }

    public function withProgram(TrainingProgram $program)
    {
        return $this->state(function (array $attributes) use ($program) {
            return [
                'training_program_id' => $program->id,
            ];
        });
    }
}
