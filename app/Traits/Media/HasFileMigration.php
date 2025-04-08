<?php

namespace App\Traits\Media;

use App\Models\Alternative;
use App\Models\Company;
use App\Models\Investor;
use App\Models\Person;
use App\Services\FileService;
use App\Supports\MediaLibrary\CustomPathGenerator;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\Console\Output\ConsoleOutput;
use Throwable;

trait HasFileMigration
{
    /**
     * Validate that the provided model instance is valid.
     *
     * @param  mixed  $model
     *
     * @throws Exception
     * @throws Throwable
     */
    protected function validateModel(Company|Alternative|Investor|Person $model): void
    {
        throw_unless($model, new Exception('Model instance not provided or invalid.'));
    }

    /**
     * Retrieve media items associated with the model.
     *
     * @param  Model  $model
     * @return Collection
     */
    protected function getModelMedia($model)
    {
        return $model->getMedia();
    }

    /**
     * Move a single media file to S3 and update its URL in the database.
     */
    protected function moveMediaToS3(Investor|Company|Alternative|Person $model, Media $media): void
    {
        $fileService = $this->initializeFileService();

        $localPath = $media->getPath();
        $fileName = $media->file_name;
        $s3Path = $this->generateS3Path($model, $fileName);

        try {
            DB::beginTransaction();
            $s3Url = $fileService->moveToS3($localPath, $s3Path);
            $this->updateMediaS3Disk($media);
            // $this->updateMediaS3Url($media, $s3Url);
            $this->logSuccess($s3Url);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            $this->logFailure($fileName, $e);
            throw new Exception($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Initialize the FileService instance.
     */
    protected function initializeFileService(): FileService
    {
        return new FileService;
    }

    /**
     * Generate the S3 path for a media file.
     */
    protected function generateS3Path(
        Investor|Alternative|Company|Person $model,
        string $fileName
    ): string {
        return (new CustomPathGenerator)->getPath($model->getFirstMedia()) . $fileName;
    }

    /**
     * Update the media's custom properties with the S3 URL.
     *
     * @param  Media  $media
     */
    protected function updateMediaS3Url($media, string $s3Url): void
    {
        $media->update(['custom_properties->s3_url' => $s3Url]);
    }

    /**
     * Update media moved to s3 with suitable to for moving
     */
    protected function updateMediaS3Disk(Media $media): void
    {
        $media->update([
            'disk' => 's3',
            'conversions_disk' => 's3',
            'generated_conversions' => [],
        ]);
    }

    /**
     * Log successful file movement to S3.
     */
    protected function logSuccess(string $s3Url): void
    {
        Log::info('File successfully moved to S3', ['s3Url' => $s3Url]);
    }

    /**
     * Log failure during file movement to S3.
     */
    protected function logFailure(string $fileName, Exception $e): void
    {
        Log::error('Failed to move file to S3', [
            'fileName' => $fileName,
            'error' => $e->getMessage(),
        ]);
    }

    /**
     * Move files associated with a model to S3 storage.
     *
     * @throws Exception
     * @throws Throwable
     */
    public function moveModelFilesToS3(): void
    {
        $this->validateModel($this);

        $mediaItems = $this->getModelMedia($this);

        foreach ($mediaItems as $media) {
            if ($media->disk !== 's3') {
                $this->moveMediaToS3($this, $media);
            } else {
                $message = "<info>  Skipping media with id: {$media->id} because it is already on S3.</info>";
                Log::alert(Str::replace('<info>', '', $message));

                (new ConsoleOutput)->writeln($message);
            }
        }
    }
}
