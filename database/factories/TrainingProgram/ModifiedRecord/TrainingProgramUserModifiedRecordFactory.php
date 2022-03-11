<?php

namespace Database\Factories\TrainingProgram\ModifiedRecord;

use App\Models\Auth\User;
use App\Models\TrainingProgram\ModifiedRecord\TrainingProgramUserModifiedRecord;
use App\Models\TrainingProgram\TrainingProgram;
use Illuminate\Database\Eloquent\Factories\Factory;

class TrainingProgramUserModifiedRecordFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TrainingProgramUserModifiedRecord::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'action' => $this->faker->randomElement(TrainingProgramUserModifiedRecord::ACTIONS),
            'training_program_id' => TrainingProgram::factory()->create()->id,
            'user_id' => User::factory()->create()->id,
            'phone_number' => $this->faker->phoneNumber(),
            'group_name' => $this->faker->text(10),
            'created_by' => User::factory()->create()->id,
        ];
    }
}
