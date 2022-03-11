<?php

namespace Tests\Unit\Models\Workflow;

use App\Models\Workflow\ThresholdForm;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ThresholdFormTest extends TestCase
{
    use RefreshDatabase;

    public function testModelsCanBeInstantiated(): void
    {
        $thresholdForm = ThresholdForm::factory()->make();
        $this->assertTrue($thresholdForm instanceof ThresholdForm);
    }

    public function testSaveToDatabases(): void
    {
        $thresholdForm = ThresholdForm::factory()->make();
        $thresholdForm->save();
        $this->assertIsNumeric($thresholdForm->id);
    }
}
