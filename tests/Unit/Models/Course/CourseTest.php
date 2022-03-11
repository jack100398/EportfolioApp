<?php

namespace Tests\Unit\Models\Course;

use App\Models\Course\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseTest extends TestCase
{
    use RefreshDatabase;

    public function testModelsCanBeInstantiated(): void
    {
        $course = Course::factory()->make();

        $this->assertTrue($course  instanceof Course);
    }

    public function testSaveToDatabase(): void
    {
        $course = Course::factory()->make();

        $course->save();

        $this->assertIsNumeric($course->id);
    }

    public function testSaveManyToDataBase(): void
    {
        Course::factory(3)->create();

        $courseCount = Course::count();

        $this->assertTrue($courseCount === 3);
    }
}
