<?php

namespace App\Supports\MediaLibrary;

use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

class CustomPathGenerator implements PathGenerator
{
    protected function getBasePath(Media $media): string
    {
        $prefix = config('media-library.prefix', '');

        if ($prefix !== '') {
            return $prefix . '/' . $media->getKey();
        }

        return $media->getKey();
    }

    /*
     * Get the path for the given media, relative to the root storage path.
     */
    public function getPath(Media $media): string
    {
        $imagesFolder = 'images';

        return $this->getPathFromModelType($media, $imagesFolder);
    }

    /*
     * Get the path for conversions of the given media, relative to the root storage path.
     */
    public function getPathForConversions(Media $media): string
    {
        $imagesFolder = 'images';
        $optimizedString = Str::finish('optimized/', '/');

        return $this->getPathFromModelType($media, $imagesFolder, $optimizedString);
    }

    /*
     * Get the path for responsive images of the given media, relative to the root storage path.
     */
    public function getPathForResponsiveImages(Media $media): string
    {
        $imagesFolder = 'images';
        $responsiveString = Str::finish('responsive-images/', '/');

        return $this->getPathFromModelType($media, $imagesFolder, $responsiveString);
    }

    /*
     * Get a unique base path for the given media, imageFolder, and addedString
     */
    private function getPathFromModelType(Media $media, string $imagesFolder, ?string $addedString = null): string
    {
        return match ($media->model_type) {
            'person' => "$imagesFolder/people/$addedString",
            'company' => "$imagesFolder/companies/$addedString",
            'investor' => "$imagesFolder/investors/$addedString",
            'alternative' => "$imagesFolder/alternatives/$addedString",
            default => "$imagesFolder"
        };
    }
}
