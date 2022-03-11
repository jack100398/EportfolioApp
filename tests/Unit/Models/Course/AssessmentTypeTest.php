<?php

namespace Tests\Unit\Models\Course;

use App\Models\Course\AssessmentType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AssessmentTypeTest extends TestCase
{
    use RefreshDatabase;

    public function testModelsCanBeInstantiated(): void
    {
        $assessmentType = AssessmentType::factory()->make();

        $this->assertTrue($assessmentType  instanceof AssessmentType);
    }

    public function testSaveToDatabase(): void
    {
        $assessmentType = AssessmentType::factory()->make();

        $assessmentType->save();

        $this->assertIsNumeric($assessmentType->id);
    }
}
