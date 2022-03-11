<?php

namespace Tests\Unit\Models\Course\Survey;

use App\Models\Course\Survey\Survey;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SurveyTest extends TestCase
{
    use RefreshDatabase;

    public function testModelsCanBeInstantiated(): void
    {
        $survey = Survey::factory()->make();

        $this->assertTrue($survey  instanceof Survey);
    }

    public function testSaveToDatabase(): void
    {
        $this->assertIsNumeric(Survey::factory()->create()->id);
    }

    public function testSaveManyToDataBase(): void
    {
        Survey::factory(30)->create();

        $this->assertTrue(Survey::count() === 30);
    }
}
