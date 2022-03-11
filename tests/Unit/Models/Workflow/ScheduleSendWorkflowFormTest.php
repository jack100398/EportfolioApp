<?php

namespace Tests\Unit\Models\Workflow;

use App\Models\Workflow\ScheduleSendWorkflowForm;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScheduleSendWorkflowFormTest extends TestCase
{
    use RefreshDatabase;

    public function testModelsCanBeInstantiated(): void
    {
        $scheduleSendWorkflowForm = ScheduleSendWorkflowForm::factory()->make();
        $this->assertTrue($scheduleSendWorkflowForm instanceof ScheduleSendWorkflowForm);
    }

    public function testSaveToDatabases(): void
    {
        $scheduleSendWorkflowForm = ScheduleSendWorkflowForm::factory()->make();
        $scheduleSendWorkflowForm->save();
        $this->assertIsNumeric($scheduleSendWorkflowForm->id);
    }
}
