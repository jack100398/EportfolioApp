<?php

namespace Database\Factories\TrainingProgram;

use App\Models\Auth\User;
use App\Models\TrainingProgram\TrainingProgram;
use App\Models\TrainingProgram\TrainingProgramUser;
use Illuminate\Database\Eloquent\Factories\Factory;

class TrainingProgramUserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TrainingProgramUser::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'training_program_id' => TrainingProgram::factory()->create()->id,
            'user_id' => User::factory()->create()->id,
            'phone_number' => $this->faker->phoneNumber(),
            'group_name' => $this->faker->text(10),
        ];
    }
}
