<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Services\FileService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TestUploadFilesToR2 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:r2-upload';

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
        //        $filePath = $this->argument('file');
        //        $driver = $this->option('driver');
        //        $disk = $this->option('disk');
        //        $bucket = $this->option('bucket') ?? config('filesystems.disks.s3.bucket');

        //        $company = Company::find(100);

        // Initialize the FileService with dynamic parameters
        //        $fileService = new FileService($driver, $disk, $bucket);

        try {
            // Upload the file and get the URL
            //            $fileUrl = $fileService->upload($filePath, basename($filePath));

            //            $iconPath = Storage::disk('s3')->put('services/icons', $filePath, 'public');
            //            $fileUrl = Storage::disk('s3')->url($iconPath);

            //            $this->info('File uploaded successfully.');
            //            $this->info("Uploaded file URL: {$fileUrl}");

            $movedFile = $this->moveCompanyFilesToS3(100);

            //            $this->info("Uploaded file URL: {$movedFile}");

        } catch (Exception $e) {
            $this->error('Failed to upload file: ' . $e->getMessage());

            return 1;
        }

        return 0;
    }

    public function moveCompanyFilesToS3(int $companyId): void
    {
        $company = Company::query()->find($companyId);

        throw_unless($company, new Exception("Company not found with ID: {$companyId}"));

        $fileService = new FileService;

        $mediaItems = $company->getMedia();

        foreach ($mediaItems as $media) {
            $localPath = $media->getPath();
            $fileName = $media->file_name;
            $s3Path = "companies/{$companyId}/{$fileName}";

            try {
                $s3Url = $fileService->moveToS3($localPath, $s3Path);

                // Update the media item URL or any necessary fields.
                $media->update(['custom_properties->s3_url' => $s3Url]);

                Log::info($s3Url);
            } catch (Exception $e) {
                Log::error('Failed to move file to S3', ['error' => $e->getMessage()]);
            }
        }
    }
}
