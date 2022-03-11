<?php

namespace Tests\Unit\Models\Course;

use App\Models\Course\Feedback;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FeedbackTest extends TestCase
{
    public function testModelsCanBeInstantiated(): void
    {
        $feedback = Feedback::factory()->make();

        $this->assertTrue($feedback  instanceof Feedback);
    }

    public function testSaveToDatabase(): void
    {
        $this->assertIsNumeric(Feedback::factory()->create()->id);
    }
}
