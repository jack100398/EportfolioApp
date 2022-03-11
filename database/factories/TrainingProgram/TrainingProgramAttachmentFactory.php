<?php

namespace Database\Factories\TrainingProgram;

use App\Models\File;
use App\Models\TrainingProgram\TrainingProgram;
use App\Models\TrainingProgram\TrainingProgramAttachment;
use App\Models\Unit;
use DateInterval;
use Illuminate\Database\Eloquent\Factories\Factory;

class TrainingProgramAttachmentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TrainingProgramAttachment::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'training_program_id' => TrainingProgram::factory()->create()->id,
            'file_id' => File::factory()->create()->id,
            'url' => $this->faker->url(),
        ];
    }
}
