<?php

namespace App\Traits\Media;

use App\Models\Alternative;
use App\Models\Company;
use App\Models\Investor;
use App\Models\Person;
use App\Services\FileService;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

trait HasFileMigration
{
    /**
     * Validate that the provided model instance is valid.
     *
     * @param  mixed  $model
     *
     * @throws Exception
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
     *
     * @param  Model  $model
     * @param  Media  $media
     */
    protected function moveMediaToS3($model, $media): void
    {
        $fileService = $this->initializeFileService();

        $localPath = $media->getPath();
        $fileName = $media->file_name;
        $s3Path = $this->generateS3Path($model, $fileName);

        try {
            $s3Url = $fileService->moveToS3($localPath, $s3Path);
            $this->updateMediaS3Url($media, $s3Url);
            $this->logSuccess($s3Url);
        } catch (Exception $e) {
            $this->logFailure($fileName, $e);
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
     *
     * @param  Model  $model
     */
    protected function generateS3Path($model, string $fileName): string
    {
        return "{$model->getTable()}/{$model->id}/{$fileName}";
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
     * @param  Model  $model
     */
    public function moveModelFilesToS3(): void
    {
        $this->validateModel($this);
        $mediaItems = $this->getModelMedia($this);
        foreach ($mediaItems as $media) {
            $this->moveMediaToS3($this, $media);
        }
    }
}
