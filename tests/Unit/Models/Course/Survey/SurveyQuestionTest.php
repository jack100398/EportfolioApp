<?php

namespace Tests\Unit\Models\Course\Survey;

use App\Models\Course\Survey\SurveyQuestion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SurveyQuestionTest extends TestCase
{
    use RefreshDatabase;

    public function testModelsCanBeInstantiated(): void
    {
        $surveyQuestion = SurveyQuestion::factory()->make();

        $this->assertTrue($surveyQuestion  instanceof SurveyQuestion);
    }

    public function testSaveToDatabase(): void
    {
        $this->assertIsNumeric(SurveyQuestion::factory()->create()->id);
    }

    public function testSaveManyToDataBase(): void
    {
        SurveyQuestion::factory(30)->create();

        $this->assertTrue(SurveyQuestion::count() === 30);
    }
}
