<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;

/**
 * get image filename archive path without extension
 *
 * This is used to associating media again after migrate:fresh from existing files
 * and I don't have to download it from url all the time
 */
function get_image_archive_path(mixed $data, $archiveFolder): string
{
    return Storage::path("images-archive/$archiveFolder/" . pathinfo((string) $data)['filename']);
}

/**
 * Associate images for the model by adding the image path
 *
 * @throws FileDoesNotExist
 * @throws FileIsTooBig
 */
function add_image_for_model(string $imagePath, $model): bool
{
    /**
     * Delete old media if there is another media added from the archive folder
     * This prevents corruption when new media is uploaded.
     */
    if (File::exists($imagePath . '.jpeg')) {
        $model->media()->delete();

        return (bool) $model->addMedia($imagePath . '.jpeg')->preservingOriginal()->toMediaCollection();
    }
    if (File::exists($imagePath . '.jpg')) {
        $model->media()->delete();

        return (bool) $model->addMedia($imagePath . '.jpg')->preservingOriginal()->toMediaCollection();
    }
    if (File::exists($imagePath . '.png')) {
        $model->media()->delete();

        return (bool) $model->addMedia($imagePath . '.png')->preservingOriginal()->toMediaCollection();
    }
    if (File::exists($imagePath . '.webp')) {
        $model->media()->delete();

        return (bool) $model->addMedia($imagePath . '.webp')->preservingOriginal()->toMediaCollection();
    }

    if (File::exists($imagePath . '.gif')) {
        $model->media()->delete();

        return (bool) $model->addMedia($imagePath . '.gif')->preservingOriginal()->toMediaCollection();
    }

    return false;
}

function add_image_urls_to_notes(?string $url, Model $model, $class): bool
{
    if (is_null($url)) {
        return false;
    }

    if (! Str::isUrl($url)) {
        return false;
    }

    $oldNotes = collect(json_decode($model->notes, true));

    $newNote = [
        'url' => $url,
        'date' => now()->toDateTimeString(),
        'class' => class_basename($class),
    ];

    // Append the new note (if old notes exist, merge them)
    $appendedNotes = $oldNotes->isEmpty()
        ? collect([$newNote])  // If old notes are empty, just create a new collection with the new note
        : $oldNotes->push($newNote);  // Otherwise, push the new note to the collection

    return $model->update(['notes' => $appendedNotes->toJson()]);
}
