<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class BringMediaToDefaultLocationsCommandsCommand extends Command
{
    protected $signature = 'media-to-default-locations';

    public function handle(): void
    {
        $this->moveDefaultImagesToDefaultLocations();

        dd('after default');

        $medias = Media::query()->get();

        $this->info("We have {$medias->count()} to add");

        $imagesFolder = 'images';

        foreach ($medias as $media) {
            $sourcePath = storage_path('app/public/') .
                $this->getPathFromModelType($media, $imagesFolder, $media->file_name);
            $destinationPath = $media->getPath();

            $this->info("Preparing to move {$media->file_name}...");

            // Ensure destination directory exists
            $destinationDir = dirname((string) $destinationPath);
            if (! File::exists($destinationDir)) {
                File::makeDirectory($destinationDir, 0755, true);
                $this->info("Created directory: {$destinationDir}");
            }

            // Now check if source file exists and copy
            if (File::exists($sourcePath)) {
                File::copy($sourcePath, $destinationPath);
                $this->info("Copied to {$destinationPath}");
            } else {
                $this->error("File not found at {$sourcePath}");
            }
        }
    }

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

    private function moveDefaultImagesToDefaultLocations(): void
    {
        $defaultCompanyMedia = storage_path('app/public/images/companies/default/company.webp');
        $defaultPersonMedia = storage_path('app/public/images/people/default/user.webp');

        File::copy($defaultCompanyMedia, storage_path('app/public/company-default.webp'));
        File::copy($defaultPersonMedia, storage_path('app/public/person-default.webp'));
    }
}
