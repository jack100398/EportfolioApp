<?php

namespace Tests\Unit\Models;

use App\Models\File;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FileTest extends TestCase
{
    use RefreshDatabase;

    public function testModels1CanBeInstantiated(): void
    {
        $file = File::factory()->make();

        $this->assertTrue($file instanceof File);
    }

    public function testSaveToDatabase(): void
    {
        $file = File::factory()->make();

        $file->save();

        $this->assertIsNumeric($file->id);
    }
}
