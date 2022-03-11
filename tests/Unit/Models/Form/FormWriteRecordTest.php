<?php

namespace Tests\Unit\Models\Form;

use App\Models\Form\FormWriteRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FormWriteRecordTest extends TestCase
{
    use RefreshDatabase;

    public function testModelsCanBeInstantiated(): void
    {
        $formWriteRecord = FormWriteRecord::factory()->make();
        $this->assertTrue($formWriteRecord instanceof FormWriteRecord);
    }

    public function testSaveToDatabase(): void
    {
        $formWriteRecord = FormWriteRecord::factory()->make();
        $formWriteRecord->save();
        $this->assertIsNumeric($formWriteRecord->id);
    }
}
