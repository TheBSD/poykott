<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;

trait HasImagePath
{
    protected function imagePath(string $collection = 'default'): Attribute
    {
        return Attribute::make(
            get: fn () => $this->resolveImagePathWithCache($collection)
        );
    }

    public function clearImagesPathCache(): void
    {
        foreach ($this->mediaCollections as $collection) {
            $cacheKey = $this->generateImagePathCacheKey($collection);
            Cache::forget($cacheKey);
        }
    }

    private function resolveImagePathWithCache(string $collection): string
    {
        $cacheKey = $this->generateImagePathCacheKey($collection);
        $cacheTTL = (int) config('cache.default_ttl', 6);

        return Cache::remember($cacheKey, now()->addHours($cacheTTL), function () use ($collection) {
            return $this->resolveImagePath($collection);
        });
    }

    private function generateImagePathCacheKey(string $collection): string
    {
        return 'image_path:' . $this::class . ':' . $this->getKey() . ':' . $collection;
    }

    private function resolveImagePath(string $collection): string
    {
        $firstMedia = $this->getMedia($collection)->first();

        if (! $firstMedia) {
            return $this->getDefaultImageUrl();
        }

        if (config('media-library.disk_name') === 's3') {
            return $this->resolveS3ImageUrl($firstMedia);
        }

        return $this->resolveLocalImageUrl($firstMedia);
    }

    private function resolveS3ImageUrl($media): string
    {
        return $media->hasGeneratedConversion('optimized')
            ? $media->getUrl('optimized')
            : $media->getUrl();
    }

    private function resolveLocalImageUrl($media): string
    {
        return file_exists($media->getPath('optimized'))
            ? $media->getUrl('optimized')
            : ($media->getPath() && file_exists($media->getPath()) ? $media->getUrl() : $this->getDefaultImageUrl());
    }

    private function getDefaultImageUrl(): string
    {
        static $defaultUrl = null;

        return $defaultUrl ??= URL::asset($this->getDefaultImagePath());
    }
}
