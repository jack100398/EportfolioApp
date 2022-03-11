<?php

namespace Tests\Unit\Models\Workflow;

use App\Models\Workflow\ManualForm;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManualFormTest extends TestCase
{
    use RefreshDatabase;

    public function testModelsCanBeInstantiated(): void
    {
        $manualForm = ManualForm::factory()->make();
        $this->assertTrue($manualForm instanceof ManualForm);
    }

    public function testSaveToDatabases(): void
    {
        $manualForm = ManualForm::factory()->make();
        $manualForm->save();
        $this->assertIsNumeric($manualForm->id);
    }
}
