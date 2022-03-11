<?php

namespace Tests\Unit\Models\Form;

use App\Models\Form\Form;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FormTest extends TestCase
{
    use RefreshDatabase;

    public function testModelsCanBeInstantiated(): void
    {
        $form = Form::factory()->make();
        $this->assertTrue($form instanceof Form);
    }

    public function testSaveToDatabases(): void
    {
        $form = Form::factory()->make();
        $form->save();
        $this->assertIsNumeric($form->id);
    }
}
