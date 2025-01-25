<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class FileService
{
    public static function processMultipleFilesFromRequest($filesInRequest): array
    {
        $processedFiles = [];

        foreach ($filesInRequest as $file) {
            $processedFiles[] = self::storeFileInDisk($file);
        }

        return $processedFiles;
    }

    public static function processMultipleFilesWithReturnedUrl($filesInRequest, string $path = 'services'): array
    {
        $processedFiles = [];

        foreach ($filesInRequest as $file) {
            $processedFiles[] = [
                'url' => self::storeFileInDisk($file, $path),
            ];
        }

        return $processedFiles;
    }

    public static function processSingleFileFromRequest($fileInRequest): string
    {
        return self::storeFileInDisk($fileInRequest);
    }

    public static function storeFileInDisk($file, string $path = 'order/images', string $disk = 'public', string $driver = 's3'): string
    {
        $createdFile = Storage::disk($driver)->put($path, $file, $disk);

        return self::getFileUrl($createdFile);
    }

    public static function getFileUrl(string $path, $driver = 's3'): string
    {
        return Storage::disk($driver)->url($path);
    }
}
