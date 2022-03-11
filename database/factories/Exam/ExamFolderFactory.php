<?php

namespace Database\Factories\Exam;

use App\Models\Auth\User;
use App\Models\Exam\ExamFolder;
use Database\Factories\Helper\FactoryHelper;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExamFolderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ExamFolder::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $parent_id = $this->faker->boolean()
            ? FactoryHelper::getRandomModelId(ExamFolder::class)
            : null;

        return [
            'name'      => $this->faker->text(10),
            'parent_id' => $parent_id,
            'created_by' => User::factory()->create()->id,
            'type'      => $this->faker->numberBetween(1, 2),
        ];
    }
}
