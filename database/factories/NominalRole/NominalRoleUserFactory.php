<?php

namespace Database\Factories\NominalRole;

use App\Models\Auth\User;
use App\Models\Course\Course;
use App\Models\NominalRole\NominalRole;
use App\Models\NominalRole\NominalRoleUser;
use App\Models\TrainingProgram\TrainingProgram;
use App\Models\TrainingProgram\TrainingProgramStep;
use App\Models\TrainingProgram\TrainingProgramUnit;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

class NominalRoleUserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = NominalRoleUser::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $roleable = $this->getRandomMorph();

        return [
            'user_id' => User::factory()->create(['deleted_at'=>null])->id,
            'nominal_role_id' => NominalRole::factory()->create()->id,
            'roleable_type' => get_class($roleable),
            'roleable_id' => $roleable->id,
        ];
    }

    private function getRandomMorph(): Model
    {
        $hash = [
            TrainingProgram::class,
            TrainingProgramStep::class,
            TrainingProgramUnit::class,
            Course::class,
        ];

        return $this->faker->randomElement($hash)::factory()->create();
    }
}
