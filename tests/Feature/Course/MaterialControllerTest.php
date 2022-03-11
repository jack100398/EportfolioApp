<?php

namespace Tests\Feature\Course;

use App\Models\Auth\User;
use App\Models\File;
use App\Models\Material\Material;
use App\Models\Unit;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Tests\TestCase;

class MaterialControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);

        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );
    }

    public function testCanShowIndex()
    {
        $response = $this->get('/api/material');

        $response->assertOk();
    }

    public function testCreateMaterial()
    {
        Storage::fake('local');
        $directory = 'avatars';

        $response = $this->post('/api/material', [
            'directory' => $directory,
            'type' => 0,
            'source' => UploadedFile::fake()->image('avatar.jpg'),
        ]);
        $response->assertCreated();

        $material = Material::find($response->json('id'));

        Storage::disk('local')
            ->assertExists($directory.'/'.$material->source);
    }

    public function testUpdateMaterial()
    {
        $id = Material::factory()->create(['type' => 0])->id;

        $folderId = Material::factory()->create()->id;

        $response = $this->put('/api/material/'.$id, [
            'folder_id' => $folderId,
            'source' => 'Testing',
        ]);
        $response->assertNoContent();

        $this->assertTrue(Material::find($id)->folder_id === $folderId);
        $this->assertTrue(File::find(Material::find($id)->source)->name === 'Testing');
    }

    public function testShowMaterial()
    {
        $response = $this->get('/api/material/'.Material::factory()->create()->id);
        $response->assertOk();
    }

    public function testDeleteCourse()
    {
        $response = $this->delete('/api/material/'.Material::factory()->create()->id);
        $response->assertNoContent();
    }

    public function testCanAuthUser()
    {
        $response = $this->post('/api/material/authUser', [
            'id' => Material::factory()->create()->id,
            'targetId' => User::factory()->create(['deleted_at' => null])->id,
        ]);
        $response->assertNoContent();
    }

    public function testCanAuthUnit()
    {
        $response = $this->post('/api/material/authUnit', [
            'id' => Material::factory()->create()->id,
            'targetId' => Unit::factory()->create()->id,
        ]);
        $response->assertNoContent();
    }

    public function testCantDownloadEmptyFolder()
    {
        $response = $this->get('/api/material/downloadMaterialFolder/'.Material::factory()->create()->id);

        $response->assertForbidden();
    }

    public function testCanDownload()
    {
        Storage::fake('local');
        $directory = 'avatars';

        $createResponse = $this->post('/api/material', [
            'directory' => $directory,
            'type' => 0,
            'source' => UploadedFile::fake()->image('avatar.jpg'),
        ]);
        $createResponse->assertCreated();

        $material = Material::find($createResponse->json('id'));

        Storage::disk('local')
            ->assertExists($directory.'/'.$material->source);

        $response = $this->get('/api/material/downloadMaterial/'.$createResponse->json('id'));

        $this->assertTrue($response->baseResponse instanceof StreamedResponse);
    }

    public function testCanDownloadZip()
    {
        Storage::fake('local');

        $directory = 'materials';

        $folderResponse = $this->post('/api/material', [
            'directory' => $directory,
            'type' => 2,
            'source' => 'testing',
        ]);
        $folderResponse->assertCreated();

        $childFolderResponse = $this->post('/api/material', [
            'directory' => $directory,
            'folder_id' => $folderResponse->json('id'),
            'type' => 2,
            'source' => 'child',
        ]);
        $childFolderResponse->assertCreated();

        $fileResponse = $this->post('/api/material', [
            'directory' => $directory,
            'folder_id' => $childFolderResponse->json('id'),
            'type' => 0,
            'source' => UploadedFile::fake()->image('avatar.jpg'),
        ]);
        $fileResponse->assertCreated();

        $material = Material::find($fileResponse->json('id'));

        Storage::disk('local')
            ->assertExists($directory.'/'.$material->source);

        $response = $this->get('/api/material/downloadMaterialFolder/'.$folderResponse->json('id'));

        $this->assertTrue($response->baseResponse instanceof BinaryFileResponse);
    }
}
