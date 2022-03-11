<?php

namespace Tests\Unit\Models\Auth;

use App\Models\Admin\UserMetadata;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserMetadataTest extends TestCase
{
    use RefreshDatabase;

    public function testModelsCanBeInstantiated(): void
    {
        $metadata = UserMetadata::factory()->make();

        $this->assertTrue($metadata instanceof UserMetadata);
    }

    public function testSaveToDatabase(): void
    {
        $metadata = UserMetadata::factory()->create();

        $this->assertIsNumeric($metadata->id);
    }
}
