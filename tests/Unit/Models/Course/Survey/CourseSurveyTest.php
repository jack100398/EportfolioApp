<?php

namespace Tests\Unit\Models\Course\Survey;

use App\Models\Course\Survey\CourseSurvey;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CourseSurveyTest extends TestCase
{
    use RefreshDatabase;

    public function testModelsCanBeInstantiated(): void
    {
        $courseSurvey = CourseSurvey::factory()->make();

        $this->assertTrue($courseSurvey  instanceof CourseSurvey);
    }

    public function testSaveToDatabase(): void
    {
        $this->assertIsNumeric(CourseSurvey::factory()->create()->id);
    }

    public function testSaveManyToDataBase(): void
    {
        CourseSurvey::factory(30)->create();

        $this->assertTrue(CourseSurvey::count() === 30);
    }
}
