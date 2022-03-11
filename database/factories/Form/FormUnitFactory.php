<?php

namespace Database\Factories\Form;

use App\Models\Form\Form;
use App\Models\Form\FormUnit;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

class FormUnitFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = FormUnit::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'form_id' => Form::factory()->create()->id,
            'unit_id' => Unit::factory()->create()->id,
        ];
    }
}
