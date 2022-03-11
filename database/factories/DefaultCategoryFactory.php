<?php

namespace Database\Factories;

use App\Models\DefaultCategory;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

class DefaultCategoryFactory extends Factory
{
    protected $model = DefaultCategory::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'parent_id' => null,
            'school_year' => $this->faker->numberBetween(90, 120),
            'unit_id' => Unit::factory()->create()->id,
            'name' => $this->faker->company(),
        ];
    }
}
