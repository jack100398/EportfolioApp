<?php

namespace Tests\Unit\Models\Course;

use App\Models\Course\CourseTarget;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CourseTargetTest extends TestCase
{
    use RefreshDatabase;

    public function testModelsCanBeInstantiated(): void
    {
        $courseTarget = CourseTarget::factory()->make();

        $this->assertTrue($courseTarget  instanceof CourseTarget);
    }

    public function testSaveToDatabase(): void
    {
        $courseTarget = CourseTarget::factory()->make();

        $courseTarget->save();

        $this->assertIsNumeric($courseTarget->id);
    }
}
