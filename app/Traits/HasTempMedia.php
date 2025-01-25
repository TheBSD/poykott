<?php

namespace App\Traits;

use App\Models\TempMedia;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasTempMedia
{
    /**
     * Get all temporary media for the model.
     */
    public function tempMedia(): MorphMany
    {
        return $this->morphMany(TempMedia::class, 'mediable');
    }

    /**
     * Add a temporary media URL to the model.
     */
    public function addTempMedia(
        string $url,
        string $collection = 'default',
        string $disk = 'public',
        array $meta = []
    ): TempMedia {
        return $this->tempMedia()->create([
            'url' => $url,
            'collection_name' => $collection,
            'disk' => $disk,
            'meta' => $meta,
        ]);
    }

    /**
     * Get all unprocessed media for a specific collection.
     */
    public function getUnprocessedMedia(string $collection = 'default')
    {
        return $this->tempMedia()
            ->where('collection_name', $collection)
            ->unprocessed()
            ->get();
    }

    /**
     * Process all unprocessed media in a collection.
     * This method should be called after successfully adding media to Spatie Media Library.
     */
    public function markMediaAsProcessed(string $collection = 'default'): int
    {
        return $this->tempMedia()
            ->where('collection_name', $collection)
            ->unprocessed()
            ->update(['is_processed' => true]);
    }
}
