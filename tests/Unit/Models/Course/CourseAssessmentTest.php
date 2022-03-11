<?php

namespace Tests\Unit\Models\Course;

use App\Models\Course\CourseAssessment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CourseAssessmentTest extends TestCase
{
    use RefreshDatabase;

    public function testModelsCanBeInstantiated(): void
    {
        $courseAssessment = CourseAssessment::factory()->make();

        $this->assertTrue($courseAssessment  instanceof CourseAssessment);
    }

    public function testSaveToDatabase(): void
    {
        $courseAssessment = CourseAssessment::factory()->make();

        $courseAssessment->save();

        $this->assertIsNumeric($courseAssessment->id);
    }
}
