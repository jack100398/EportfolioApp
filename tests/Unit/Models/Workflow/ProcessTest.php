<?php

namespace Tests\Unit\Models\Workflow;

use App\Models\Workflow\Process;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProcessTest extends TestCase
{
    use RefreshDatabase;

    public function testModelsCanBeInstantiated(): void
    {
        $process = Process::factory()->make();

        $this->assertTrue($process instanceof Process);
    }

    public function testSaveToDatabase(): void
    {
        $process = Process::factory()->make();
        $process->save();
        $this->assertIsNumeric($process->id);
    }
}
