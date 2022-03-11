<?php

namespace Tests\Feature;

use App\Models\Auth\User;
use App\Services\File\StorageDirectoryEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class FileControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );
    }

    public function testFileUpload()
    {
        Storage::fake('local');
        $directory = 'avatars';

        $response = $this->post('/api/files', [
            'directory' => $directory,
            'file' => UploadedFile::fake()->image('avatar.jpg'),
        ])
            ->assertCreated()
            ->assertJsonStructure(['id']);

        Storage::disk('local')
            ->assertExists($directory.'/'.$response->json('id'));
    }

    public function testFileUploadMissingDirectory()
    {
        $this->post('/api/files', [
            'file' => UploadedFile::fake()->image('avatar.jpg'),
        ])->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
