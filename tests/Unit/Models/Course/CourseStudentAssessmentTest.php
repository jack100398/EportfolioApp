<?php

namespace Tests\Unit\Models\Course;

use App\Models\Course\CourseStudentAssessment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CourseStudentAssessmentTest extends TestCase
{
    use RefreshDatabase;

    public function testModelsCanBeInstantiated(): void
    {
        $studentAssessment = CourseStudentAssessment::factory()->make();

        $this->assertTrue($studentAssessment  instanceof CourseStudentAssessment);
    }

    public function testSaveToDatabase(): void
    {
        $this->assertIsNumeric(CourseStudentAssessment::factory()->create()->id);
    }

    public function testSaveManyToDataBase(): void
    {
        CourseStudentAssessment::factory(30)->create();

        $this->assertTrue(CourseStudentAssessment::count() === 30);
    }
}
