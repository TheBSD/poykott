<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

trait HasImagePath
{
    /**
     * Internal method to handle image path logic
     *
     * @param  string  $collection  Collection name for the media (optional)
     * @param  string  $defaultPath  Default image path if no media found
     */
    protected function resolveImagePath(
        string $collection = 'default',
        string $defaultPath = 'storage/images/companies/default/company.webp',
    ): Attribute {
        return Attribute::make(
            get: function () use ($collection, $defaultPath) {

                if (config('media-library.disk_name') === 's3') {
                    return $this->resolveImagePathForS3($collection, $defaultPath);
                }

                $firstMedia = $this->getMedia($collection)->first();

                $optimizedPath = $firstMedia?->getPath('optimized');
                $optimizedUrl = $firstMedia?->getUrl('optimized');

                $originalPath = $firstMedia?->getPath();
                $originalUrl = $firstMedia?->getUrl();

                $defaultUrl = URL::asset($defaultPath);

                if ($optimizedPath && file_exists($optimizedPath)) {
                    return $optimizedUrl;
                }

                if ($originalPath && file_exists($originalPath)) {
                    return $originalUrl;
                }

                return $defaultUrl;
            }
        );
    }

    /**
     * Retrieves the appropriate image path:
     * - First checks for an optimized version.
     * - If the optimized version is not available, it checks for the original image.
     * - If neither the optimized nor original image is available, a default image path is returned.
     */
    protected function imagePath(): Attribute
    {
        return $this->resolveImagePath();
    }

    private function resolveImagePathForS3($collection, $defaultPath)
    {
        $firstMedia = $this->getMedia($collection)->first();

        $optimizedPath = $firstMedia?->getPath('optimized');
        $optimizedUrl = $firstMedia?->getUrl('optimized');

        $originalPath = $firstMedia?->getPath();
        $originalUrl = $firstMedia?->getUrl();

        $defaultUrl = URL::asset($defaultPath);

        if ($optimizedPath && Storage::disk('s3')->exists($optimizedPath)) {
            return $optimizedUrl;
        }

        if ($originalPath && Storage::disk('s3')->exists($originalPath)) {
            return $originalUrl;
        }

        return $defaultUrl;
    }
}
