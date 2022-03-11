<?php

namespace Tests\Unit\Models\Workflow;

use App\Models\Workflow\Workflow;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function testModelsCanBeInstantiated(): void
    {
        $workflow = Workflow::factory()->make();

        $this->assertTrue($workflow instanceof Workflow);
    }

    public function testSaveToDatabase(): void
    {
        $workflow = Workflow::factory()->make();
        $workflow->save();
        $this->assertIsNumeric($workflow->id);
    }
}
