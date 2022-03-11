<?php

namespace Database\Factories\NominalRole;

use App\Models\NominalRole\NominalRole;
use Illuminate\Database\Eloquent\Factories\Factory;

class NominalRoleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = NominalRole::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->colorName(),
            'type' => $this->faker->randomElement(collect(NominalRole::TYPES)->keys()),
            'is_active' => $this->faker->boolean(),
        ];
    }
}
