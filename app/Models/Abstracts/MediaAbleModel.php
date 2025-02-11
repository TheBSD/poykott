<?php

namespace App\Models\Abstracts;

use App\Traits\HasImagePath;
use App\Traits\Media\HasFileMigration;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

abstract class MediaAbleModel extends Model implements HasMedia
{
    use HasFileMigration;
    use HasImagePath;
    use InteractsWithMedia;

    protected static function boot()
    {
        parent::boot();
        static::updated(function (): void {
            $this->clearImagesPathCache();
        });
    }

    abstract public function getDefaultImagePath(): string;

    abstract public function registerMediaCollections(): void;
}
