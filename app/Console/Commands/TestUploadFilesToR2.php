<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class TestUploadFilesToR2 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:r2-upload {file : The path to the file to upload}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test file upload to Cloudflare R2';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $filePath = $this->argument('file');

        // Check if the file exists
        if (! file_exists($filePath)) {
            $this->error("File does not exist at path: {$filePath}");

            return 1;
        }

        $fileName = basename($filePath); // Extract only the file name
        $fileContents = file_get_contents($filePath);

        try {
            // Upload the file to the R2 disk
            Storage::disk('r2')->put($fileName, $fileContents, 'public');
            $this->info("File {$fileName} uploaded successfully to Cloudflare R2.");

            // Construct the public URL manually
            $bucketName = config('filesystems.disks.r2.bucket');
            $endpoint = config('filesystems.disks.r2.endpoint');
            $fileUrl = "{$endpoint}/{$bucketName}/{$fileName}";

            $this->info("Uploaded file URL: {$fileUrl}");

        } catch (Exception $e) {
            $this->error('Failed to upload file: ' . $e->getMessage());

            return 1;
        }

        return 0;
    }
}
