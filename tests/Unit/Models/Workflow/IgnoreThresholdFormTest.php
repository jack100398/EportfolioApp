<?php

namespace Tests\Unit\Models\Workflow;

use App\Models\Workflow\IgnoreThresholdForm;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IgnoreThresholdFormTest extends TestCase
{
    use RefreshDatabase;

    public function testModelsCanBeInstantiated(): void
    {
        $ignoreThresholdForm = IgnoreThresholdForm::factory()->make();
        $this->assertTrue($ignoreThresholdForm instanceof IgnoreThresholdForm);
    }

    public function testSaveToDatabases(): void
    {
        $ignoreThresholdForm = IgnoreThresholdForm::factory()->make();
        $ignoreThresholdForm->save();
        $this->assertIsNumeric($ignoreThresholdForm->id);
    }
}
