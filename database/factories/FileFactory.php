<?php

namespace Database\Factories;

use App\Models\File;
use Illuminate\Database\Eloquent\Factories\Factory;

class FileFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = File::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'extension' => $this->faker->fileExtension(),
            'size'=>$this->faker->numberBetween(),
            'directory' => 'avatars',
            'remarks'=>$this->faker->realText(),
            'created_by'=>$this->faker->numberBetween(),
        ];
    }
}
