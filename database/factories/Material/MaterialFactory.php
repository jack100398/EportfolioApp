<?php

namespace Database\Factories\Material;

use App\Models\Auth\User;
use App\Models\File;
use App\Models\Material\Material;
use Illuminate\Database\Eloquent\Factories\Factory;

class MaterialFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Material::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'folder_id' => null,
            'type' => 0,
            'source' => File::factory()->create()->id,
            'owner' => User::factory()->create()->id,
        ];
    }
}
