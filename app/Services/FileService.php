<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class FileService
{
    public function __construct(protected string $driver = 'local', protected string $disk = 'public', protected ?string $bucket = null) {}

    protected function configureDiskWithBucket(string $bucket): void
    {
        config([
            "filesystems.disks.{$this->disk}.bucket" => $bucket,
        ]);
    }

    protected function constructUrl(string $fileName): string
    {
        $endpoint = config("filesystems.disks.{$this->disk}.endpoint");
        $bucketName = config("filesystems.disks.{$this->disk}.bucket");

        return "{$endpoint}/{$bucketName}/{$fileName}";
    }

    /**
     * @throws Throwable
     */
    public function upload(string $filePath, string $fileName): string
    {
        Log::info('Uploading file', [
            'filePath' => $filePath,
            'fileName' => $fileName,
            'bucket' => $this->bucket,
        ]);

        throw_unless(file_exists($filePath), new Exception("File does not exist at path: {$filePath}"));

        $fileContents = file_get_contents($filePath);

        if ($this->bucket !== '' && $this->bucket !== '0') {
            $this->configureDiskWithBucket($this->bucket);
        }

        $uploaded = Storage::disk($this->disk)->put($fileName, $fileContents, 'public');
        throw_unless($uploaded, new Exception("Failed to upload file: {$fileName}"));

        return $this->constructUrl($fileName);
    }

    /**
     * @throws Throwable
     */
    public function moveToS3(string $localPath, string $s3Path): string
    {
        Log::info('Moving file to S3', ['localPath' => $localPath, 's3Path' => $s3Path]);

        throw_unless(file_exists($localPath), new Exception("Local file does not exist: {$localPath}"));

        $fileContents = file_get_contents($localPath);

        $uploaded = Storage::disk('s3')->put($s3Path, $fileContents, [
            'visibility' => 'public', // Ensure the file is publicly accessible
        ]);

        throw_unless($uploaded, new Exception("Failed to upload file to S3: {$s3Path}"));

        $s3Url = Storage::disk('s3')->url($s3Path);

        Log::info('File uploaded to S3 successfully', ['s3Url' => $s3Url]);

        return $s3Url;
    }
}
