<?php

namespace Tests\Unit\Models\Course;

use App\Models\Course\CourseMember;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseMemberTest extends TestCase
{
    use RefreshDatabase;

    public function testModelsCanBeInstantiated(): void
    {
        $courseMember = CourseMember::factory()->make();

        $this->assertTrue($courseMember  instanceof CourseMember);
    }

    public function testSaveToDatabase(): void
    {
        $courseMember = CourseMember::factory()->make();

        $courseMember->save();

        $this->assertIsNumeric($courseMember->id);
    }
}
