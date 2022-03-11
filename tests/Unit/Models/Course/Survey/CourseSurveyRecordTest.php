<?php

namespace Tests\Unit\Models\Course\Survey;

use App\Models\Course\Survey\CourseSurveyRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CourseSurveyRecordTest extends TestCase
{
    use RefreshDatabase;

    public function testModelsCanBeInstantiated(): void
    {
        $courseSurveyRecord = CourseSurveyRecord::factory()->make();

        $this->assertTrue($courseSurveyRecord  instanceof CourseSurveyRecord);
    }

    public function testSaveToDatabase(): void
    {
        $this->assertIsNumeric(CourseSurveyRecord::factory()->create()->id);
    }

    public function testSaveManyToDataBase(): void
    {
        CourseSurveyRecord::factory(30)->create();

        $this->assertTrue(CourseSurveyRecord::count() === 30);
    }
}
