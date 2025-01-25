<?php

namespace App\Console\Commands;

use App\Services\FileService;
use Exception;
use Illuminate\Console\Command;

class TestUploadFilesToR2 extends Command
{
    /**
     * The name and signature of the console command.
     * ex: php artisan test:r2-upload storage/app/public/images/alternatives/01JH93MX1A5G43RC9VD8YX9X7V.webp --driver=r2 --disk=r2 --bucket=your-bucket-name
     *
     * @var string
     */
    protected $signature = 'test:r2-upload {file : The path to the file to upload} {--driver=local : The storage driver to use} {--disk=public : The storage disk to use} {--bucket= : The bucket name to upload to}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test file upload to Cloudflare R2 or other storage services';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $filePath = $this->argument('file');
        $driver = $this->option('driver');
        $disk = $this->option('disk');
        $bucket = $this->option('bucket') ?? config('filesystems.disks.r2.bucket');

        // Initialize the FileService with dynamic parameters
        $fileService = new FileService($driver, $disk, $bucket);

        try {
            // Upload the file and get the URL
            $fileUrl = $fileService->upload($filePath, basename($filePath));

            $this->info('File uploaded successfully.');
            $this->info("Uploaded file URL: {$fileUrl}");

        } catch (Exception $e) {
            $this->error('Failed to upload file: ' . $e->getMessage());

            return 1;
        }

        return 0;
    }
}
