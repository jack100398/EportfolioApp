<?php

namespace Database\Factories\Material;

use App\Models\Auth\User;
use App\Models\Material\CourseMaterial;
use App\Models\Material\MaterialDownloadHistory;
use Illuminate\Database\Eloquent\Factories\Factory;

class MaterialDownloadHistoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = MaterialDownloadHistory::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'course_material_id' => CourseMaterial::factory()->create()->id,
            'student' => User::factory()->create()->id,
            'opened_counts' => 0,
            'downloaded_counts' => 0,
            'reading_time' => '00:00:00',
        ];
    }
}
