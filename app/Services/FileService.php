<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Storage;

class FileService
{
    /**
     * Constructor to set the storage driver, disk, and bucket.
     */
    public function __construct(protected string $driver = 'local', protected string $disk = 'public', protected ?string $bucket = null) {}

    /**
     * Configure the storage disk with a specific bucket.
     */
    protected function configureDiskWithBucket(string $bucket): void
    {
        config([
            "filesystems.disks.{$this->disk}.bucket" => $bucket,
        ]);
    }

    /**
     * Construct the public URL for the uploaded file.
     */
    protected function constructUrl(string $fileName): string
    {
        $endpoint = config("filesystems.disks.{$this->disk}.endpoint");
        $bucketName = config("filesystems.disks.{$this->disk}.bucket");

        return "{$endpoint}/{$bucketName}/{$fileName}";
    }

    /**
     * Upload a file to the specified storage.
     *
     * @throws Exception
     */
    public function upload(string $filePath, string $fileName): string
    {
        throw_unless(file_exists($filePath), new Exception("File does not exist at path: {$filePath}"));

        $fileContents = file_get_contents($filePath);

        if ($this->bucket !== '' && $this->bucket !== '0') {
            $this->configureDiskWithBucket($this->bucket);
        }

        $uploaded = Storage::disk($this->disk)->put($fileName, $fileContents, 'public');
        throw_unless($uploaded, new Exception("Failed to upload file: {$fileName}"));

        return $this->constructUrl($fileName);
    }
}
