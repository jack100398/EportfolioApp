<?php

namespace Tests\Unit\Models\Material;

use App\Models\Material\MaterialDownloadHistory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MaterialDownloadHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function testModelsCanBeInstantiated(): void
    {
        $history = MaterialDownloadHistory::factory()->create();

        $this->assertTrue($history instanceof MaterialDownloadHistory);
    }

    public function testSaveToDatabase(): void
    {
        $history = MaterialDownloadHistory::factory()->create();

        $this->assertIsNumeric($history->id);
    }
}
