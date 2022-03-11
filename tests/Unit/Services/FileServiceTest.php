<?php

namespace Tests\Unit\Services;

use App\Models\Auth\User;
use App\Models\File;
use App\Services\File\FileService;
use App\Services\File\StorageDirectoryEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FileServiceTest extends TestCase
{
    use RefreshDatabase;

    public function testGetFileInfo(): void
    {
        $file = File::factory()->create();

        $fileService = new FileService();

        $this->assertInstanceOf(File::class, $fileService->getFileInfo($file->id));
    }

    public function testFileCanBeSave(): void
    {
        Storage::fake('local');

        $file = UploadedFile::fake()->image('avatar.jpg')->size(100);

        $fileService = new FileService();
        $id = $fileService->save(StorageDirectoryEnum::AVATAR, $file, User::factory()->create()->id);

        $raw = File::find($id);

        Storage::disk('local')->assertExists($raw->directory.'/'.$raw->id);
    }
}
