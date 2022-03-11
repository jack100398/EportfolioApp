<?php

namespace Database\Factories\Material;

use App\Models\Auth\User;
use App\Models\Course\Course;
use App\Models\Material\CourseMaterial;
use App\Models\Material\Material;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseMaterialFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CourseMaterial::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'course_id' => Course::factory()->create()->id,
            'material_id' => Material::factory()->create()->id,
            'description' => $this->faker->name(),
            'required_time' => '00:00:15',
            'opened_at' => now()->addDays(5),
            'ended_at' => now()->addDays(10),
            'created_by' => User::factory()->create()->id,
            'updated_by' => User::factory()->create()->id,
        ];
    }

    public function hasMaterial(): HasMany
    {
        return $this->hasMany(Material::class, 'material_id');
    }

    public function belongCourse(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }
}
