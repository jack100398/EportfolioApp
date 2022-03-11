<?php

namespace App\Services\File;

use App\Models\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileService
{
    public function getFileInfo(int $fileId): File
    {
        return File::findOrFail($fileId);
    }

    /**
     * Save file.
     *
     * @param string $directory - Ref: `StorageDirectoryEnum`
     * @param UploadedFile $file
     *
     * @return int The file id is represented as file name
     */
    public function save(string $directory, UploadedFile $file, int $userId): int
    {
        $id = $this->saveUploadInformation($directory, $file, $userId);

        Storage::disk('local')->putFileAs($directory, $file, strval($id));

        return $id;
    }

    public function createZipStream(string $zipName): \ZipArchive
    {
        $zipFile = $zipName;
        $zip = new \ZipArchive();
        $zip->open($zipFile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        return $zip;
    }

    public function endZipStream(\ZipArchive &$zip): void
    {
        $zip->close();
    }

    public function pushFileIntoFolder(string $rootDir, int $fileId, string $currentPath, \ZipArchive $zip): \ZipArchive
    {
        $file = File::findOrFail($fileId);
        if (Storage::disk('local')->exists("$rootDir/{$file->id}")) {
            $zip->addFile(
                Storage::path("$rootDir/{$file->id}"),
                "${currentPath}/{$file->name}.{$file->extension}"
            );
        } elseif (Storage::disk('local')->exists("$rootDir/{$file->id}.{$file->extension}")) {
            $zip->addFile(
                Storage::path("$rootDir/{$file->id}.{$file->extension}"),
                "${currentPath}/{$file->name}.{$file->extension}"
            );
        }

        return $zip;
    }

    private function saveUploadInformation(string $directory, UploadedFile $file, int $userId): int
    {
        $f = new File();
        $f->name = $file->getClientOriginalName();
        $f->extension = $file->extension();
        $f->size = $file->getSize();
        $f->directory = $directory;

        $f->created_by = $userId;

        $f->save();

        return $f->id;
    }
}
