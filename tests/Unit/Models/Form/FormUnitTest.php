<?php

namespace Tests\Unit\Models\Form;

use App\Models\Form\FormUnit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FormUnitTest extends TestCase
{
    use RefreshDatabase;

    public function testModelsCanBeInstantiated(): void
    {
        $formUnit = FormUnit::factory()->make();

        $this->assertTrue($formUnit instanceof FormUnit);
    }

    public function testSaveToDatabase(): void
    {
        $formUnit = FormUnit::factory()->make();
        $formUnit->save();
        $this->assertIsNumeric($formUnit->id);
    }
}
