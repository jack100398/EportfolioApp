<?php

namespace Tests\Unit\Models\Course;

use App\Models\Course\Course;
use App\Models\Course\CourseHistory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CourseHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function testModelsCanBeInstantiated(): void
    {
        $courseHistory = CourseHistory::factory()->make();

        $this->assertTrue($courseHistory  instanceof CourseHistory);
    }

    public function testSaveToDatabase(): void
    {
        $courseId = Course::factory()->create()->id;

        $courseHistory = CourseHistory::factory()->make(['course_id' => $courseId]);

        $courseHistory->save();

        $this->assertIsNumeric($courseHistory->id);
    }
}
