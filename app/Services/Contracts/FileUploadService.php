<?php

namespace App\Services\Contracts;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileUploadService
{
    public function uploadFile($file, string $directory): string
    {
        $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
        return $file->storeAs($directory, $fileName, 'public');
    }

    public function uploadMultipleFiles(array $files, string $directory): array
    {
        $uploadedFiles = [];

        foreach ($files as $file) {
            $uploadedFiles[] = $this->uploadFile($file, $directory);
        }
        return $uploadedFiles;
    }

    public function deleteFile(string $filePath): bool
    {
        return Storage::disk('public')->delete($filePath);
    }

    public function deleteMultipleFiles(array $filesPaths): bool
    {
        return Storage::disk('public')->delete($filesPaths);
    }
}
