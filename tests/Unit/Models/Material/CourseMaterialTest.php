<?php

namespace Tests\Unit\Models\Material;

use App\Models\Material\CourseMaterial;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CourseMaterialTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testModelsCanBeInstantiated(): void
    {
        $courseMaterial = CourseMaterial::factory()->create();

        $this->assertTrue($courseMaterial  instanceof CourseMaterial);
    }

    public function testSaveToDatabase(): void
    {
        $courseMaterial = CourseMaterial::factory()->create();

        $this->assertIsNumeric($courseMaterial->id);
    }
}
