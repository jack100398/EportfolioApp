<?php

namespace Tests\Unit\Models\Workflow;

use App\Models\Workflow\DefaultWorkflow;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DefaultWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function testModelsCanBeInstantiated(): void
    {
        $defaultWorkflow = DefaultWorkflow::factory()->make();

        $this->assertTrue($defaultWorkflow instanceof DefaultWorkflow);
    }

    public function testSaveToDatabase(): void
    {
        $defaultWorkflow = DefaultWorkflow::factory()->make();
        $defaultWorkflow->save();
        $this->assertIsNumeric($defaultWorkflow->id);
    }
}
