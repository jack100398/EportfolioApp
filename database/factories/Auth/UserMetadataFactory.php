<?php

namespace Database\Factories\Auth;

use App\Models\Admin\UserMetadata;
use App\Models\Auth\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserMetadataFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserMetadata::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => fn () =>  User::factory()->create()->id,
            'key' => 'city',
            'value' => $this->faker->city,
        ];
    }
}
